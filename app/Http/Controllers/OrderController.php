<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_data' => 'required|json',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|regex:/^[927][0-9]{7}$/',
            'prescription_number' => 'required|string|max:50',
            'payment_method' => 'required|in:orange_money,mtn_money,moov_money',
            'client_docs' => 'required|array|min:1',
            'client_docs.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        // Décoder les données de la commande
        $orderData = json_decode($request->order_data, true);
        $totalAmount = array_sum(array_map(fn($center) => $center['total_amount'], $orderData));
        $toPayAmount = $totalAmount * 0.5; // 50% du montant total

        // Créer la commande principale
        $order = Order::create([
            'user_id' => auth()->id(),
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'prescription_number' => $request->prescription_number,
            'total_amount' => $totalAmount,
            'paid_amount' => $toPayAmount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending'
        ]);

        // Créer les éléments de la commande pour chaque centre
        foreach ($orderData as $centerData) {
            foreach ($centerData['items'] as $item) {
                $orderItem = $order->items()->create([
                    'center_id' => $centerData['center_id'],
                    'blood_type' => $item['blood_type'],
                    'quantity' => $item['quantity'],
                    'unit_price' => 5000, // Prix unitaire fixe
                    'total_price' => $item['quantity'] * 5000,
                    'paid_amount' => ($item['quantity'] * 5000) * 0.5, // 50% du montant
                    'remaining_amount' => ($item['quantity'] * 5000) * 0.5, // 50% restant
                    'status' => 'pending'
                ]);

                // Créer la validation pour le centre
                OrderValidation::create([
                    'order_item_id' => $orderItem->id,
                    'center_id' => $centerData['center_id'],
                    'status' => 'pending',
                    'is_primary' => true
                ]);
            }
        }

        // Sauvegarder les documents
        $paths = [];
        foreach ($request->file('client_docs') as $file) {
            $path = $file->store('orders/' . $order->id, 'public');
            $paths[] = $path;
        }
        $order->update(['documents' => $paths]);

        // Simuler le paiement (à remplacer par l'intégration réelle du paiement mobile)
        try {
            // Simulation d'un paiement réussi
            $paymentReference = 'PAY-' . strtoupper(Str::random(10));
            $order->update([
                'payment_reference' => $paymentReference,
                'payment_status' => 'completed'
            ]);

            // Notifier les centres concernés
            foreach ($order->items as $item) {
                // Envoyer une notification au centre
                $center = $item->center;
                $center->notify(new NewOrderNotification($order, $item));
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement effectué et commande enregistrée avec succès',
                'payment_reference' => $paymentReference,
                'order' => $order->load('items.center')
            ]);
        } catch (\Exception $e) {
            // En cas d'échec du paiement
            $order->update(['payment_status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Le paiement a échoué. Veuillez réessayer.'
            ], 422);
        }
    }

    /**
     * Display a listing of orders for validation.
     */
    public function index()
    {
        $orders = Order::with(['validations', 'center'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['validations.center', 'center']);
        return view('orders.show', compact('order'));
    }

    /**
     * Update order validation status.
     */
    public function updateValidation(Request $request, Order $order, OrderValidation $validation)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comment' => 'nullable|string|max:500'
        ]);

        $validation->update([
            'status' => $request->status,
            'comment' => $request->comment,
            'validated_at' => now()
        ]);

        // Si toutes les validations sont approuvées, marquer la commande comme approuvée
        if ($request->status === 'approved' && $order->validations()->where('status', '!=', 'approved')->count() === 0) {
            $order->update(['status' => 'approved']);
        }
        // Si une validation est rejetée, marquer la commande comme rejetée
        elseif ($request->status === 'rejected') {
            $order->update(['status' => 'rejected']);
        }

        return back()->with('success', 'Validation mise à jour avec succès');
    }

    /**
     * Cancel the order.
     */
    public function cancel(Order $order)
    {
        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']);
            return back()->with('success', 'Commande annulée avec succès');
        }

        return back()->with('error', 'Cette commande ne peut plus être annulée');
    }

    /**
     * Track order by reservation number and email.
     */
    public function track(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|integer|exists:orders,id',
            'client_email' => 'required|email'
        ]);

        $order = Order::with(['items.center'])
            ->where('id', $request->reservation_id)
            ->where('client_email', $request->client_email)
            ->first();

        if (!$order) {
            return back()
                ->withInput()
                ->with('error', 'Aucune réservation trouvée avec ces informations.');
        }

        // Préparer les statistiques
        $stats = [
            'total' => 1,
            'pending' => $order->status === 'pending' ? 1 : 0,
            'confirmed' => $order->status === 'confirmed' ? 1 : 0,
            'completed' => $order->status === 'completed' ? 1 : 0,
            'cancelled' => $order->status === 'cancelled' ? 1 : 0
        ];

        return view('reservations.result', [
            'order' => $order,
            'stats' => $stats
        ]);
    }
}
