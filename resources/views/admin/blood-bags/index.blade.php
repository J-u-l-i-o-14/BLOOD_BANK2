@extends('layouts.app')

@section('title', 'Gestion des poches de sang')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Stock des poches de sang</h2>
                    <div class="flex gap-4">
                        <a href="{{ route('blood-bags.create') }}" 
                           class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvelle poche
                        </a>
                        <a href="#" 
                           onclick="document.getElementById('export-form').submit()"
                           class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <i class="fas fa-download mr-2"></i>
                            Exporter
                        </a>
                        <form id="export-form" action="{{ route('blood-bags.export') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Filtres -->
                <form action="{{ route('blood-bags.index') }}" method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-1">Groupe sanguin</label>
                        <select name="blood_type" id="blood_type" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Tous</option>
                            @foreach($bloodTypes as $type)
                                <option value="{{ $type->id }}" {{ request('blood_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="status" id="status" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Tous</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                            <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Réservée</option>
                            <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Utilisée</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirée</option>
                        </select>
                    </div>
                    <div>
                        <label for="expiry" class="block text-sm font-medium text-gray-700 mb-1">Expiration</label>
                        <select name="expiry" id="expiry" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Toutes</option>
                            <option value="expired" {{ request('expiry') == 'expired' ? 'selected' : '' }}>Expirées</option>
                            <option value="expiring_soon" {{ request('expiry') == 'expiring_soon' ? 'selected' : '' }}>Expire bientôt (7 jours)</option>
                            <option value="valid" {{ request('expiry') == 'valid' ? 'selected' : '' }}>Valides</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-filter mr-2"></i>Filtrer
                        </button>
                    </div>
                </form>

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Poches disponibles</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['available'] }}</p>
                            </div>
                            <div class="text-green-500">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Poches réservées</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['reserved'] }}</p>
                            </div>
                            <div class="text-yellow-500">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Poches utilisées</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['used'] }}</p>
                            </div>
                            <div class="text-blue-500">
                                <i class="fas fa-hand-holding-medical text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Poches expirées</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['expired'] }}</p>
                            </div>
                            <div class="text-red-500">
                                <i class="fas fa-exclamation-circle text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                    <table class="border-collapse table-auto w-full bg-white">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700">
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">ID</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Groupe sanguin</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Centre</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Date prélèvement</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Date expiration</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Statut</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($bloodBags as $bag)
                                <tr class="hover:bg-gray-50 {{ $bag->isExpiring() ? 'bg-yellow-50' : '' }} {{ $bag->isExpired() ? 'bg-red-50' : '' }}">
                                    <td class="py-4 px-6">#{{ $bag->id }}</td>
                                    <td class="py-4 px-6">{{ $bag->blood_type->name }}</td>
                                    <td class="py-4 px-6">{{ $bag->center->name }}</td>
                                    <td class="py-4 px-6">{{ $bag->collection_date->format('d/m/Y') }}</td>
                                    <td class="py-4 px-6">
                                        <span class="{{ $bag->isExpired() ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $bag->expiry_date->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-3 py-1 text-sm rounded-full {{ 
                                            match($bag->status) {
                                                'available' => 'bg-green-100 text-green-800',
                                                'reserved' => 'bg-yellow-100 text-yellow-800',
                                                'used' => 'bg-blue-100 text-blue-800',
                                                'expired' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            }
                                        }}">
                                            {{ match($bag->status) {
                                                'available' => 'Disponible',
                                                'reserved' => 'Réservée',
                                                'used' => 'Utilisée',
                                                'expired' => 'Expirée',
                                                default => 'Inconnu'
                                            } }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('blood-bags.show', $bag) }}" 
                                               class="text-blue-600 hover:text-blue-900"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($bag->status === 'available')
                                                <a href="{{ route('blood-bags.edit', $bag) }}" 
                                                   class="text-yellow-600 hover:text-yellow-900"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('blood-bags.destroy', $bag) }}" 
                                                      method="POST" 
                                                      class="inline-block"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette poche ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 px-6 text-center text-gray-500">
                                        Aucune poche de sang trouvée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $bloodBags->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
