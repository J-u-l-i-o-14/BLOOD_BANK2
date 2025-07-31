@extends('layouts.main')

@section('page-title', 'Créer une campagne de dons')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow p-8">
    <h2 class="text-2xl font-bold mb-6">Nouvelle campagne de dons</h2>
    <form method="POST" action="{{ route('campagne.store') }}">
        @csrf
        <div class="mb-4">
            <label for="title" class="block font-medium mb-1">Titre</label>
            <input type="text" name="title" id="title" class="w-full border rounded px-3 py-2" required value="{{ old('title') }}">
        </div>
        <div class="mb-4">
            <label for="name" class="block font-medium mb-1">Nom interne (obligatoire)</label>
            <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2" required value="{{ old('name') }}">
        </div>
        <div class="mb-4">
            <label for="description" class="block font-medium mb-1">Description</label>
            <textarea name="description" id="description" class="w-full border rounded px-3 py-2" required>{{ old('description') }}</textarea>
        </div>
        <div class="mb-4">
            <label for="date" class="block font-medium mb-1">Date</label>
            <input type="date" name="date" id="date" class="w-full border rounded px-3 py-2" required value="{{ old('date') }}">
        </div>
        <div class="flex justify-end">
            <button type="submit" class="btn btn-red">Créer la campagne</button>
        </div>
    </form>
</div>
@endsection
