@extends('layouts.main')

@section('page-title', 'Enregistrer un don')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow border border-gray-200 p-6">
    <h2 class="text-lg font-semibold mb-4">Enregistrer un don</h2>
    <form action="{{ route('dons.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="donor_id" class="block text-sm font-medium text-gray-700">Donneur</label>
            <input type="number" name="donor_id" id="donor_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="campaign_id" class="block text-sm font-medium text-gray-700">Campagne (optionnel)</label>
            <input type="number" name="campaign_id" id="campaign_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label for="blood_bag_id" class="block text-sm font-medium text-gray-700">Sac de sang (optionnel)</label>
            <input type="number" name="blood_bag_id" id="blood_bag_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label for="donated_at" class="block text-sm font-medium text-gray-700">Date et heure du don</label>
            <input type="datetime-local" name="donated_at" id="donated_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="volume" class="block text-sm font-medium text-gray-700">Volume (ml)</label>
            <input type="number" name="volume" id="volume" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (optionnel)</label>
            <textarea name="notes" id="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Enregistrer</button>
    </form>
</div>
@endsection
