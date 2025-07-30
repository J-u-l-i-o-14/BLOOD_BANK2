@extends('layouts.main')

@section('page-title', 'Voir les campagnes de dons')

@section('page-actions')
<a href="{{ route('campagne.create') }}" class="btn btn-red">Créer une campagne</a>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <h2 class="text-2xl font-semibold mb-6">Campagnes de dons</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($campagnes as $campagne)
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold mb-2">{{ $campagne->title }}</h3>
                    <p class="text-gray-700 mb-4">{{ $campagne->description }}</p>
                </div>
                <div class="mt-2 text-sm text-gray-500">Date : {{ $campagne->date }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
