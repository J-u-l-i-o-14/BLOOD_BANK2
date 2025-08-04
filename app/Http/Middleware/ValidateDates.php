<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class ValidateDates
{
    /**
     * Validate dates in the request to prevent invalid dates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Liste des champs de date à valider
        $dateFields = [
            'birth_date' => [
                'min' => Carbon::now()->subYears(120), // Pas plus de 120 ans
                'max' => Carbon::now()->subYears(16)   // Minimum 16 ans
            ],
            'appointment_date' => [
                'min' => Carbon::tomorrow(), // À partir de demain
                'max' => Carbon::now()->addMonths(3) // Maximum 3 mois dans le futur
            ],
            'collection_date' => [
                'min' => Carbon::now()->subDays(2), // Maximum 2 jours dans le passé
                'max' => Carbon::now() // Pas de dates futures
            ],
            'expiry_date' => [
                'min' => Carbon::now(), // Pas de dates passées
                'max' => Carbon::now()->addYears(1) // Maximum 1 an dans le futur
            ],
            'campaign_start_date' => [
                'min' => Carbon::tomorrow(),
                'max' => Carbon::now()->addYears(1)
            ],
            'campaign_end_date' => [
                'min' => Carbon::tomorrow(),
                'max' => Carbon::now()->addYears(1)
            ]
        ];

        foreach ($dateFields as $field => $constraints) {
            if ($request->has($field)) {
                $date = Carbon::parse($request->input($field));
                
                // Vérifier les contraintes min/max
                if ($date->lt($constraints['min']) || $date->gt($constraints['max'])) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'message' => "La date {$field} n'est pas valide",
                            'errors' => [
                                $field => ["La date doit être entre {$constraints['min']->format('d/m/Y')} et {$constraints['max']->format('d/m/Y')}"]
                            ]
                        ], 422);
                    }

                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors([
                            $field => "La date doit être entre {$constraints['min']->format('d/m/Y')} et {$constraints['max']->format('d/m/Y')}"
                        ]);
                }

                // Validation spécifique pour les dates de campagne
                if ($field === 'campaign_end_date' && $request->has('campaign_start_date')) {
                    $startDate = Carbon::parse($request->input('campaign_start_date'));
                    if ($date->lt($startDate)) {
                        if ($request->wantsJson()) {
                            return response()->json([
                                'message' => 'La date de fin doit être après la date de début',
                                'errors' => [
                                    'campaign_end_date' => ['La date de fin doit être après la date de début']
                                ]
                            ], 422);
                        }

                        return redirect()
                            ->back()
                            ->withInput()
                            ->withErrors([
                                'campaign_end_date' => 'La date de fin doit être après la date de début'
                            ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
