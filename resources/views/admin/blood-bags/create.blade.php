@extends('layouts.app')

@section('title', 'Nouvelle poche de sang')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Ajouter une nouvelle poche de sang</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Remplissez les informations ci-dessous. Tous les champs marqués d'une * sont obligatoires.
                    </p>
                </div>

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('blood-bags.store') }}" method="POST" class="max-w-2xl">
                    @csrf
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Groupe sanguin -->
                            <div>
                                <label for="blood_type_id" class="block text-sm font-medium text-gray-700">
                                    Groupe sanguin <span class="text-red-500">*</span>
                                </label>
                                <select name="blood_type_id" id="blood_type_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Sélectionner un groupe</option>
                                    @foreach($bloodTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('blood_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Centre -->
                            <div>
                                <label for="center_id" class="block text-sm font-medium text-gray-700">
                                    Centre <span class="text-red-500">*</span>
                                </label>
                                <select name="center_id" id="center_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Sélectionner un centre</option>
                                    @foreach($centers as $center)
                                        <option value="{{ $center->id }}" {{ old('center_id') == $center->id ? 'selected' : '' }}>
                                            {{ $center->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Date de prélèvement -->
                        <div>
                            <label for="collection_date" class="block text-sm font-medium text-gray-700">
                                Date de prélèvement <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="collection_date" id="collection_date" required
                                   value="{{ old('collection_date') }}"
                                   max="{{ now()->format('Y-m-d') }}"
                                   min="{{ now()->subDays(2)->format('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <p class="mt-1 text-sm text-gray-500">
                                La date ne peut pas être antérieure à 2 jours ou postérieure à aujourd'hui
                            </p>
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-center gap-4 pt-5">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-md">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer
                            </button>
                            <a href="{{ route('blood-bags.index') }}" class="inline-flex items-center px-4 py-2 text-gray-700 hover:text-gray-900">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation de la date de prélèvement
    const dateInput = document.getElementById('collection_date');
    const maxDate = new Date().toISOString().split('T')[0];
    const minDate = new Date();
    minDate.setDate(minDate.getDate() - 2);
    const minDateString = minDate.toISOString().split('T')[0];

    dateInput.setAttribute('max', maxDate);
    dateInput.setAttribute('min', minDateString);

    dateInput.addEventListener('input', function(e) {
        const selectedDate = new Date(e.target.value);
        const today = new Date();
        const twoDaysAgo = new Date();
        twoDaysAgo.setDate(today.getDate() - 2);

        if (selectedDate > today) {
            e.target.setCustomValidity('La date ne peut pas être dans le futur');
        } else if (selectedDate < twoDaysAgo) {
            e.target.setCustomValidity('La date ne peut pas être antérieure à 2 jours');
        } else {
            e.target.setCustomValidity('');
        }
    });
});
</script>
@endpush
@endsection
