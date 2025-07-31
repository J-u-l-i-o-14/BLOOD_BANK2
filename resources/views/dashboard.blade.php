@extends('layouts.main')

@section('page-title', 'Tableau de bord')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                {{ __("Vous êtes connecté !") }}
            </div>
        </div>
    </div>
@endsection
