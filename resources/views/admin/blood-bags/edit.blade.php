@extends('layouts.app')

@section('title', 'Modifier la poche de sang')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Modifier la poche de sang #{{ $bloodBag->id }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Modifier les informations de la poche de sang
                    </p>
                </div>

                <form action="{{ route('blood-bags.update', $bloodBag) }}" method="POST" class="max-w-3xl">
                    @csrf
                    @method('PUT')

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Attention : La modification de certaines informations peut affecter la traçabilité de la poche de sang.
                                    Assurez-vous que les modifications sont nécessaires et conformes aux protocoles.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Type sanguin -->
                        <div>
                            <label for="blood_type_id" class="block text-sm font-medium text-gray-700">
                                Type sanguin <span class="text-red-500">*</span>
                            </label>
                            <select id="blood_type_id" 
                                    name="blood_type_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    required>
                                @foreach($bloodTypes as $type)
                                    <option value="{{ $type->id }}" {{ $bloodBag->blood_type_id == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('blood_type_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Centre de collecte -->
                        <div>
                            <label for="center_id" class="block text-sm font-medium text-gray-700">
                                Centre de collecte <span class="text-red-500">*</span>
                            </label>
                            <select id="center_id" 
                                    name="center_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    required>
                                @foreach($centers as $center)
                                    <option value="{{ $center->id }}" {{ $bloodBag->center_id == $center->id ? 'selected' : '' }}>
                                        {{ $center->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('center_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    required>
                                <option value="available" {{ $bloodBag->status === 'available' ? 'selected' : '' }}>Disponible</option>
                                <option value="reserved" {{ $bloodBag->status === 'reserved' ? 'selected' : '' }}>Réservée</option>
                                <option value="used" {{ $bloodBag->status === 'used' ? 'selected' : '' }}>Utilisée</option>
                                <option value="expired" {{ $bloodBag->status === 'expired' ? 'selected' : '' }}>Expirée</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date de prélèvement -->
                        <div>
                            <label for="collection_date" class="block text-sm font-medium text-gray-700">
                                Date de prélèvement <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="collection_date" 
                                   name="collection_date" 
                                   value="{{ old('collection_date', $bloodBag->collection_date->format('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                   required>
                            @error('collection_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date d'expiration -->
                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700">
                                Date d'expiration <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="expiry_date" 
                                   name="expiry_date" 
                                   value="{{ old('expiry_date', $bloodBag->expiry_date->format('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                   required>
                            @error('expiry_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du donneur</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom du donneur -->
                            <div>
                                <label for="donor_name" class="block text-sm font-medium text-gray-700">
                                    Nom du donneur <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="donor_name" 
                                       name="donor_name" 
                                       value="{{ old('donor_name', $bloodBag->donor_name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                       required>
                                @error('donor_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Téléphone du donneur -->
                            <div>
                                <label for="donor_phone" class="block text-sm font-medium text-gray-700">
                                    Téléphone du donneur <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" 
                                       id="donor_phone" 
                                       name="donor_phone" 
                                       value="{{ old('donor_phone', $bloodBag->donor_phone) }}"
                                       pattern="[0-9]{10}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                       required>
                                @error('donor_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="flex justify-end gap-4">
                            <a href="{{ route('blood-bags.show', $bloodBag) }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="bg-primary-500 hover:bg-primary-600 text-white font-bold py-2 px-4 rounded">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mise à jour automatique de la date d'expiration
        const collectionDateInput = document.getElementById('collection_date');
        const expiryDateInput = document.getElementById('expiry_date');
        
        collectionDateInput.addEventListener('change', function() {
            if (this.value) {
                const collectionDate = new Date(this.value);
                const expiryDate = new Date(collectionDate);
                expiryDate.setDate(collectionDate.getDate() + 42); // 42 jours de conservation
                
                const year = expiryDate.getFullYear();
                const month = String(expiryDate.getMonth() + 1).padStart(2, '0');
                const day = String(expiryDate.getDate()).padStart(2, '0');
                
                expiryDateInput.value = `${year}-${month}-${day}`;
            }
        });

        // Validation du statut en fonction de la date d'expiration
        const statusSelect = document.getElementById('status');
        
        function updateStatusBasedOnExpiry() {
            const today = new Date();
            const expiryDate = new Date(expiryDateInput.value);
            
            if (expiryDate < today && statusSelect.value !== 'expired') {
                alert('Attention : Cette poche est expirée. Le statut va être mis à jour automatiquement.');
                statusSelect.value = 'expired';
            }
        }

        expiryDateInput.addEventListener('change', updateStatusBasedOnExpiry);
        
        // Validation du formulaire
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            const collectionDate = new Date(collectionDateInput.value);
            const expiryDate = new Date(expiryDateInput.value);
            const today = new Date();

            if (collectionDate > today) {
                event.preventDefault();
                alert('La date de prélèvement ne peut pas être dans le futur.');
                return;
            }

            if (expiryDate <= collectionDate) {
                event.preventDefault();
                alert('La date d\'expiration doit être postérieure à la date de prélèvement.');
                return;
            }

            const diffTime = Math.abs(expiryDate - collectionDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 42) {
                event.preventDefault();
                alert('La durée de conservation ne peut pas dépasser 42 jours.');
                return;
            }
        });
    });
</script>
@endpush
