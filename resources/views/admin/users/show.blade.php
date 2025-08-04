@extends('layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">Détails de l'utilisateur</h2>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('users.edit', $user) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"
                                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-trash mr-2"></i>Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-4">Informations générales</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">#{{ $user->id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nom</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Rôle</dt>
                                    <dd class="mt-1">
                                        <span class="px-3 py-1 text-sm rounded-full {{ 
                                            match($user->role) {
                                                'admin' => 'bg-purple-100 text-purple-800',
                                                'manager' => 'bg-blue-100 text-blue-800',
                                                'donor' => 'bg-green-100 text-green-800',
                                                'patient' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            }
                                        }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                    <dd class="mt-1">
                                        <span class="px-3 py-1 text-sm rounded-full {{ 
                                            $user->is_active 
                                            ? 'bg-green-100 text-green-800' 
                                            : 'bg-red-100 text-red-800'
                                        }}">
                                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-800 mb-4">Statistiques & Activité</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $user->created_at->format('d/m/Y à H:i') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $user->updated_at->format('d/m/Y à H:i') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Dernière connexion</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : 'Jamais' }}
                                    </dd>
                                </div>

                                @if($user->role === 'donor')
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Dons effectués</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->donations_count ?? 0 }} dons
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Dernier don</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->last_donation_date ? $user->last_donation_date->format('d/m/Y') : 'Aucun' }}
                                        </dd>
                                    </div>
                                @endif

                                @if($user->role === 'patient')
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Réservations effectuées</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->reservations_count ?? 0 }} réservations
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                @if($user->role === 'donor')
                    <!-- Historique des dons -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Historique des dons</h3>
                        @if($user->donations && $user->donations->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Centre</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type sanguin</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($user->donations as $donation)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $donation->created_at->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $donation->center->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $donation->blood_type }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                                                        match($donation->status) {
                                                            'completed' => 'bg-green-100 text-green-800',
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'cancelled' => 'bg-red-100 text-red-800',
                                                            default => 'bg-gray-100 text-gray-800'
                                                        }
                                                    }}">
                                                        {{ ucfirst($donation->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Aucun don enregistré pour cet utilisateur.</p>
                        @endif
                    </div>
                @endif

                @if($user->role === 'patient')
                    <!-- Historique des réservations -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Historique des réservations</h3>
                        @if($user->reservations && $user->reservations->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Ordonnance</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Centre</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($user->reservations as $reservation)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $reservation->created_at->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $reservation->prescription_number }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $reservation->center->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ number_format($reservation->total_amount, 0, ',', ' ') }} FCFA
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                                                        match($reservation->status) {
                                                            'completed' => 'bg-green-100 text-green-800',
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'cancelled' => 'bg-red-100 text-red-800',
                                                            default => 'bg-gray-100 text-gray-800'
                                                        }
                                                    }}">
                                                        {{ ucfirst($reservation->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Aucune réservation enregistrée pour cet utilisateur.</p>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
