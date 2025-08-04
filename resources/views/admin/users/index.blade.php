@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Liste des utilisateurs</h2>
                    <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-plus mr-2"></i>Nouvel utilisateur
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filtres -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <input type="text" id="search" placeholder="Rechercher un utilisateur..." 
                               class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    </div>
                    <div>
                        <select id="role-filter" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="">Tous les rôles</option>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="donor">Donneur</option>
                            <option value="patient">Patient</option>
                        </select>
                    </div>
                    <div>
                        <select id="status-filter" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="">Tous les statuts</option>
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                    <table class="border-collapse table-auto w-full bg-white">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700">
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">ID</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Nom</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Email</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Rôle</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Statut</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-left">Créé le</th>
                                <th class="py-4 px-6 font-bold uppercase text-sm text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-6">{{ $user->id }}</td>
                                <td class="py-4 px-6">{{ $user->name }}</td>
                                <td class="py-4 px-6">{{ $user->email }}</td>
                                <td class="py-4 px-6">
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
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 text-sm rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const roleFilter = document.getElementById('role-filter');
    const statusFilter = document.getElementById('status-filter');

    // Fonction pour filtrer les lignes
    function filterTable() {
        const searchValue = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        const statusValue = statusFilter.value;
        
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const name = row.children[1].textContent.toLowerCase();
            const email = row.children[2].textContent.toLowerCase();
            const role = row.children[3].textContent.toLowerCase();
            const status = row.children[4].textContent.toLowerCase();
            const isActive = status.includes('actif');
            
            const matchesSearch = name.includes(searchValue) || email.includes(searchValue);
            const matchesRole = !roleValue || role.includes(roleValue);
            const matchesStatus = !statusValue || 
                                (statusValue === '1' && isActive) || 
                                (statusValue === '0' && !isActive);
            
            row.style.display = matchesSearch && matchesRole && matchesStatus ? '' : 'none';
        });
    }

    // Écouteurs d'événements
    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
});
</script>
@endpush
@endsection
