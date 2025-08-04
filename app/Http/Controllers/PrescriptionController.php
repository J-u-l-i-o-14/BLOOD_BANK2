<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Valide un numéro d'ordonnance
     * Vérifie si l'ordonnance peut être utilisée pour une nouvelle commande
     */
    public function validatePrescription($number)
    {
        try {
            // Chercher les commandes existantes pour ce numéro d'ordonnance
            $orders = Order::where('prescription_number', $number)
                         ->with(['centers' => function($query) {
                             $query->select('centers.id', 'status');
                         }])
                         ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => 'new',
                    'message' => 'Nouvelle ordonnance valide'
                ]);
            }

            // Vérifier si toutes les commandes sont complétées
            $allCompleted = $orders->every(function ($order) {
                return $order->centers->every(function ($center) {
                    return $center->pivot->status === 'completed';
                });
            });

            // Obtenir des statistiques sur les commandes
            $stats = [
                'total_orders' => $orders->count(),
                'completed_orders' => $orders->filter(function ($order) {
                    return $order->centers->every(function ($center) {
                        return $center->pivot->status === 'completed';
                    });
                })->count(),
                'last_order_date' => $orders->max('created_at')->format('d/m/Y')
            ];

            if ($allCompleted) {
                return response()->json([
                    'status' => 'completed',
                    'message' => 'Cette ordonnance a déjà été complètement traitée.',
                    'stats' => $stats
                ]);
            }

            return response()->json([
                'status' => 'in_progress',
                'message' => 'Cette ordonnance a des commandes en cours.',
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la validation de l\'ordonnance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère l'historique des commandes pour une ordonnance
     */
    public function getPrescriptionHistory($number)
    {
        try {
            $orders = Order::where('prescription_number', $number)
                         ->with(['centers' => function($query) {
                             $query->select('centers.id', 'centers.name', 'status', 'quantity');
                         }])
                         ->orderBy('created_at', 'desc')
                         ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'date' => $order->created_at->format('d/m/Y H:i'),
                        'centers' => $order->centers->map(function($center) {
                            return [
                                'name' => $center->name,
                                'quantity' => $center->pivot->quantity,
                                'status' => $center->pivot->status,
                            ];
                        })
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'historique: ' . $e->getMessage()
            ], 500);
        }
    }
}
