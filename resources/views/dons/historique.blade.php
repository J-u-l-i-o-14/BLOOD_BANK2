@extends('layouts.main')

@section('page-title', 'Historique des dons')

@section('content')
<div class="max-w-5xl mx-auto bg-white rounded-lg shadow border border-gray-200 p-6 mt-6">
    <h2 class="text-lg font-semibold mb-6 flex items-center"><i class="fas fa-history mr-2"></i>Historique des dons</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Donneur</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campagne</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Groupe sanguin</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($histories as $history)
                <tr>
                    <td class="px-4 py-2">
                        {{ $history->donor ? ($history->donor->first_name . ' ' . $history->donor->last_name) : '-' }}
                    </td>
                    <td class="px-4 py-2">{{ $history->campaign->title ?? '-' }}</td>
                    <td class="px-4 py-2">
                        {{ $history->bloodBag && $history->bloodBag->bloodType ? $history->bloodBag->bloodType->group : '-' }}
                    </td>
                    <td class="px-4 py-2">{{ $history->volume ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $history->donated_at ? $history->donated_at->format('d/m/Y H:i') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">Aucun historique trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $histories->links() }}
        </div>
    </div>
</div>
@endsection
