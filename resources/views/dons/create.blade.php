@extends('layouts.main')

@section('page-title', 'Enregistrer un don')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow border border-gray-200 p-6">
    <h2 class="text-lg font-semibold mb-4">Enregistrer un don</h2>
    <form action="{{ route('dons.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="donor_id" class="block text-sm font-medium text-gray-700">Nom complet du donneur</label>
            <select name="donor_id" id="donor_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">-- Sélectionner --</option>
                @foreach(\App\Models\Donor::all() as $donor)
                    <option value="{{ $donor->id }}">{{ $donor->first_name }} {{ $donor->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="campaign_id" class="block text-sm font-medium text-gray-700">Campagne (optionnel)</label>
            <select name="campaign_id" id="campaign_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">-- Aucune --</option>
                @foreach(\App\Models\Campaign::all() as $campaign)
                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="blood_bag_id" class="block text-sm font-medium text-gray-700">Groupe sanguin (sac de sang)</label>
            <select name="blood_bag_id" id="blood_bag_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">-- Sélectionner --</option>
                @foreach(\App\Models\BloodBag::with('bloodType')->get() as $bag)
                    <option value="{{ $bag->id }}">{{ $bag->bloodType ? $bag->bloodType->group : 'Inconnu' }} (Sac #{{ $bag->id }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="donated_at" class="block text-sm font-medium text-gray-700">Date et heure du don</label>
            <input type="datetime-local" name="donated_at" id="donated_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="volume" class="block text-sm font-medium text-gray-700">Quantité (ml)</label>
            <input type="number" name="volume" id="volume" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Enregistrer</button>
    </form>
</div>
@endsection
