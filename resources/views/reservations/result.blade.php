@extends('layouts.public')

@section('title', 'Résultat du suivi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- En-tête -->
            <div class="bg-white rounded-t-lg shadow-lg p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Réservation #{{ $order->id }}</h1>
                        <p class="text-gray-600">{{ $order->created_at->isoFormat('LL à HH:mm') }}</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ 
                            match($order->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            }
                        }}">
                            {{ match($order->status) {
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmée',
                                'completed' => 'Terminée',
                                'cancelled' => 'Annulée',
                                default => 'Statut inconnu'
                            } }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informations client -->
            <div class="bg-white shadow-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations du patient</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nom complet</p>
                        <p class="font-medium">{{ $order->client_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Téléphone</p>
                        <p class="font-medium">{{ $order->client_phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Email</p>
                        <p class="font-medium">{{ $order->client_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">N° Ordonnance</p>
                        <p class="font-medium">{{ $order->prescription_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Détails de la réservation -->
            <div class="bg-white shadow-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Détails de la réservation</h2>
                
                @foreach($order->items as $item)
                <div class="border-b border-gray-200 last:border-0 py-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-medium text-gray-800">{{ $item->center->name }}</h3>
                            <p class="text-gray-600">{{ $item->blood_type }} - {{ $item->quantity }} poches</p>
                            <p class="text-sm text-gray-500 mt-1">Prix unitaire: {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2 {{ 
                                match($item->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                }
                            }}">
                                {{ match($item->status) {
                                    'pending' => 'En attente',
                                    'confirmed' => 'Confirmé',
                                    'completed' => 'Terminé',
                                    'cancelled' => 'Annulé',
                                    default => 'Inconnu'
                                } }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Résumé des paiements -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Montant total</span>
                        <span class="font-semibold text-gray-800">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Montant payé</span>
                        <span class="text-green-600 font-semibold">{{ number_format($order->paid_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if($order->payment_reference)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Référence paiement</span>
                        <span class="font-mono">{{ $order->payment_reference }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white shadow-lg p-6 rounded-b-lg mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Documents fournis</h2>
                    <div>
                        @if ($order->documents_validated === true)
                            <span class="inline-flex items-center text-green-600">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Documents validés
                            </span>
                        @elseif ($order->documents_validated === false)
                            <span class="inline-flex items-center text-red-600">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Documents rejetés
                            </span>
                        @else
                            <span class="text-yellow-600">En attente de validation</span>
                        @endif
                    </div>
                </div>

                @if($order->documents_validation_comment)
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    {{ $order->documents_validation_comment }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($order->documents as $document)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm text-gray-600">Document {{ $loop->iteration }}</span>
                            </div>
                            <a href="{{ Storage::url($document) }}" 
                               target="_blank"
                               class="text-red-600 hover:text-red-800 text-sm font-medium">
                                Voir
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('reservation.tracking') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Nouvelle recherche
                </a>

                @if($order->status === 'pending' && $order->payment_status !== 'completed')
                    <button type="button" 
                            onclick="document.getElementById('cancel-confirmation').classList.remove('hidden')"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Annuler la réservation
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'annulation -->
<div id="cancel-confirmation" 
     class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden"
     x-data="{ show: false }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Confirmer l'annulation</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form action="{{ route('reservation.cancel', $order) }}" method="POST" class="sm:ml-3">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">
                            Confirmer l'annulation
                        </button>
                    </form>
                    <button type="button" 
                            onclick="document.getElementById('cancel-confirmation').classList.add('hidden')"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
