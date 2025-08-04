<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderDocumentController extends Controller
{
    /**
     * Valider les documents d'une commande.
     */
    public function validate(Request $request, Order $order)
    {
        try {
            if ($order->documents_validated) {
                return response()->json([
                    'message' => 'Les documents ont déjà été validés'
                ], 422);
            }

            $validated = $order->validateDocuments(
                validator: $request->user(),
                comment: $request->input('comment')
            );

            if (!$validated) {
                throw new \Exception('Erreur lors de la validation des documents');
            }

            return response()->json([
                'message' => 'Documents validés avec succès',
                'order' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeter les documents d'une commande.
     */
    public function reject(Request $request, Order $order)
    {
        try {
            $request->validate([
                'comment' => 'required|string|max:500'
            ]);

            if ($order->documents_validated) {
                return response()->json([
                    'message' => 'Les documents ont déjà été validés'
                ], 422);
            }

            $rejected = $order->rejectDocuments(
                validator: $request->user(),
                comment: $request->input('comment')
            );

            if (!$rejected) {
                throw new \Exception('Erreur lors du rejet des documents');
            }

            return response()->json([
                'message' => 'Documents rejetés',
                'order' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors du rejet: ' . $e->getMessage()
            ], 500);
        }
    }
}
