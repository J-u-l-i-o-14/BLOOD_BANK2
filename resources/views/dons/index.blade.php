@extends('layouts.main')

@section('page-title', 'Gestion des dons')

@section('page-actions')
<div class="flex gap-4">
    <a href="{{ route('dons.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center">
        <i class="fas fa-plus-circle mr-2"></i>Enregistrer un don
    </a>
    <a href="{{ route('dons.historique') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center">
        <i class="fas fa-history mr-2"></i>Historique
    </a>
    <a href="{{ route('dons.inscription') }}" class="bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center">
        <i class="fas fa-user-plus mr-2"></i>Inscrire un donneur
    </a>
</div>
@endsection

@section('content')
@php
    $donneurs = \App\Models\DonneurList::with('bloodType')->paginate(15);
@endphp
<div class="max-w-5xl mx-auto bg-white rounded-lg shadow border border-gray-200 p-6 mt-6">
    <h2 class="text-lg font-semibold mb-6 flex items-center"><i class="fas fa-users mr-2"></i>Liste des donneurs</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Groupe sanguin</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date de naissance</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($donneurs as $donneur)
                <tr>
                    <td class="px-4 py-2">{{ $donneur->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $donneur->email ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $donneur->phone ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $donneur->bloodType ? $donneur->bloodType->group : '-' }}</td>
                    <td class="px-4 py-2">{{ $donneur->birthdate ? \Carbon\Carbon::parse($donneur->birthdate)->format('d/m/Y') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">Aucun donneur trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $donneurs->links() }}
        </div>
    </div>
</div>
@endsection
