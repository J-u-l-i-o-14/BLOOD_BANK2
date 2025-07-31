
@extends('layouts.main')

@section('page-title', 'Créer une campagne de dons')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow border border-gray-200 p-6">
    <h2 class="text-lg font-semibold mb-4">Créer une campagne de dons</h2>
    <form action="{{ route('dons.campagne.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
            <input type="text" name="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
        </div>
        <div>
            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
            <input type="date" name="date" id="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Créer la campagne</button>
    </form>
</div>
@endsection
