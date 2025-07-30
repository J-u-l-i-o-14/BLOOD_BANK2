@extends('layouts.main')

@section('page-title', 'Inscription d\'un donneur')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow border border-gray-200 p-6">
    <h2 class="text-lg font-semibold mb-4">Inscription d'un donneur</h2>
    <form action="{{ route('dons.inscription.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="donor_id" class="block text-sm font-medium text-gray-700">Nom complet</label>
            <select name="donor_id" id="donor_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required onchange="updateDonorFields()">
                <option value="">-- Sélectionner --</option>
                @foreach(\App\Models\Donor::with('user')->get() as $donor)
                    <option value="{{ $donor->id }}" data-email="{{ $donor->email }}" data-phone="{{ $donor->phone }}" data-name="{{ $donor->first_name }} {{ $donor->last_name }}">{{ $donor->first_name }} {{ $donor->last_name }}</option>
                @endforeach
            </select>
            <input type="hidden" name="name" id="name" required>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly required>
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
            <input type="text" name="phone" id="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly>
        </div>
        <div>
            <label for="birthday" class="block text-sm font-medium text-gray-700">Date de naissance</label>
            <input type="date" name="birthday" id="birthday" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        
        <div>
            <label for="blood_type_id" class="block text-sm font-medium text-gray-700">Groupe sanguin</label>
            <select name="blood_type_id" id="blood_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">-- Sélectionner --</option>
                @foreach(\App\Models\BloodType::all() as $type)
                    <option value="{{ $type->id }}">{{ $type->group }}</option>
                @endforeach
            </select>
        </div>
        <script>
        function updateDonorFields() {
            var select = document.getElementById('donor_id');
            var email = select.options[select.selectedIndex].getAttribute('data-email');
            var phone = select.options[select.selectedIndex].getAttribute('data-phone');
            var name = select.options[select.selectedIndex].getAttribute('data-name');
            document.getElementById('email').value = email || '';
            document.getElementById('phone').value = phone || '';
            document.getElementById('name').value = name || '';
        }
        </script>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Inscrire</button>
    </form>
</div>
@endsection
