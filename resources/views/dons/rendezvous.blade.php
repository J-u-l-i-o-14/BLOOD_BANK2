@extends('layouts.main')

@section('page-title', 'Liste des rendez-vous de dons')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow border border-gray-200 p-6">
    <h2 class="text-lg font-semibold mb-4">Rendez-vous de dons</h2>
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-4 py-2">Donneur</th>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Statut</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
            <tr>
                <td class="px-4 py-2">{{ $appointment->donor->name }}</td>
                <td class="px-4 py-2">{{ $appointment->date }}</td>
                <td class="px-4 py-2">{{ $appointment->status }}</td>
                <td class="px-4 py-2 space-x-2">
                    <form action="{{ route('dons.rendezvous.confirmer', $appointment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">Confirmer</button>
                    </form>
                    <form action="{{ route('dons.rendezvous.annuler', $appointment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Annuler</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
