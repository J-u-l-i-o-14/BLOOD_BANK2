@extends('layouts.app')

@section('title', 'Détails de la poche de sang')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">
                            Poche de sang #{{ $bloodBag->id }}
                            <span class="ml-2 px-3 py-1 text-sm rounded-full {{ 
                                match($bloodBag->status) {
                                    'available' => 'bg-green-100 text-green-800',
                                    'reserved' => 'bg-yellow-100 text-yellow-800',
                                    'used' => 'bg-blue-100 text-blue-800',
                                    'expired' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                }
                            }}">
                                {{ match($bloodBag->status) {
                                    'available' => 'Disponible',
                                    'reserved' => 'Réservée',
                                    'used' => 'Utilisée',
                                    'expired' => 'Expirée',
                                    default => 'Inconnu'
                                } }}
                            </span>
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Créée le {{ $bloodBag->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    <div class="flex gap-4">
                        @if($bloodBag->status === 'available')
                            <a href="{{ route('blood-bags.edit', $bloodBag) }}" 
                               class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier
                            </a>
                            <form action="{{ route('blood-bags.destroy', $bloodBag) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette poche ?');"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                    <i class="fas fa-trash mr-2"></i>
                                    Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations de la poche -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="font-semibold text-lg text-gray-800 mb-4">Informations de la poche</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Groupe sanguin</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->blood_type->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Volume</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->quantity_ml }} ml</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Centre de collecte</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->center->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de prélèvement</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->collection_date->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date d'expiration</dt>
                                <dd class="mt-1 text-sm {{ $bloodBag->isExpired() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                    {{ $bloodBag->expiry_date->format('d/m/Y') }}
                                    @if($bloodBag->isExpiring())
                                        <span class="text-yellow-600 text-xs block">Expire bientôt</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Durée de conservation</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($bloodBag->isExpired())
                                        Expirée depuis {{ $bloodBag->expiry_date->diffForHumans() }}
                                    @else
                                        Expire {{ $bloodBag->expiry_date->diffForHumans() }}
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Informations du donneur -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="font-semibold text-lg text-gray-800 mb-4">Informations du donneur</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->donor_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->donor_phone }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($bloodBag->status === 'reserved' && $bloodBag->reservation)
                    <!-- Informations de réservation -->
                    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="font-semibold text-lg text-gray-800 mb-4">Détails de la réservation</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Client</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->reservation->client->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->reservation->client->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->reservation->client->phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">N° Ordonnance</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->reservation->prescription_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de réservation</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bloodBag->reservation->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Statut du paiement</dt>
                                <dd class="mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full {{ 
                                        $bloodBag->reservation->payment_status === 'completed'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800'
                                    }}">
                                        {{ $bloodBag->reservation->payment_status === 'completed' ? 'Payé' : 'En attente' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                @endif

                <!-- Historique -->
                <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Historique des événements</h3>
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($bloodBag->history()->orderBy('created_at', 'desc')->get() as $event)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white {{ 
                                                    match($event->type) {
                                                        'created' => 'bg-green-500',
                                                        'updated' => 'bg-blue-500',
                                                        'reserved' => 'bg-yellow-500',
                                                        'used' => 'bg-purple-500',
                                                        'expired' => 'bg-red-500',
                                                        default => 'bg-gray-500'
                                                    }
                                                }}">
                                                    <i class="fas {{ 
                                                        match($event->type) {
                                                            'created' => 'fa-plus',
                                                            'updated' => 'fa-edit',
                                                            'reserved' => 'fa-clock',
                                                            'used' => 'fa-check',
                                                            'expired' => 'fa-times',
                                                            default => 'fa-circle'
                                                        }
                                                    }} text-white"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 flex justify-between space-x-4 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-gray-500">{{ $event->description }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $event->created_at->format('Y-m-d H:i') }}">
                                                        {{ $event->created_at->format('d/m/Y H:i') }}
                                                    </time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
