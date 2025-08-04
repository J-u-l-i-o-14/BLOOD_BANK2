<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de la commande') }} #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages flash -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Informations de la commande -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informations client -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Informations client</h3>
                            <dl class="grid grid-cols-1 gap-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nom</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->client_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->client_email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->client_phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Numéro d'ordonnance</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->prescription_number }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Informations commande -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Détails de la commande</h3>
                            <dl class="grid grid-cols-1 gap-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Centre</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->center->name }} ({{ $order->center->region }})</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Groupe sanguin</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->blood_type }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Quantité</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->quantity }} poches</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Montant total</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} F CFA</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'approved') bg-green-100 text-green-800
                                            @elseif($order->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $order->status }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date de commande</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Documents -->
                    @if($order->documents)
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Documents</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($order->documents as $document)
                                    <a href="{{ Storage::url($document) }}" target="_blank" 
                                       class="block p-4 border rounded-lg hover:bg-gray-50">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-sm text-gray-600 truncate">
                                                {{ basename($document) }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Validations -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">Validations des centres</h3>
                    
                    <div class="space-y-6">
                        @foreach($order->validations as $validation)
                            <div class="border rounded-lg p-4 @if($validation->is_primary) bg-blue-50 @endif">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium">
                                            {{ $validation->center->name }}
                                            @if($validation->is_primary)
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded ml-2">Centre principal</span>
                                            @endif
                                        </h4>
                                        <p class="text-sm text-gray-600">{{ $validation->center->region }}</p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($validation->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($validation->status === 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $validation->status }}
                                    </span>
                                </div>

                                @if($validation->status === 'pending' && auth()->user()->center_id === $validation->center_id)
                                    <div class="mt-4">
                                        <form action="{{ route('orders.validations.update', [$order, $validation]) }}" method="POST">
                                            @csrf
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Commentaire (optionnel)</label>
                                                    <textarea name="comment" rows="2" 
                                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"></textarea>
                                                </div>
                                                <div class="flex space-x-4">
                                                    <button type="submit" name="status" value="approved"
                                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        Approuver
                                                    </button>
                                                    <button type="submit" name="status" value="rejected"
                                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        Rejeter
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if($validation->comment)
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p class="font-medium">Commentaire :</p>
                                        <p class="mt-1">{{ $validation->comment }}</p>
                                    </div>
                                @endif

                                @if($validation->validated_at)
                                    <div class="mt-2 text-xs text-gray-500">
                                        Validé le {{ $validation->validated_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
