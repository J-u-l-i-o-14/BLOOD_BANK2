<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Blood Bank') }} - @yield('title', 'Gestion de Stock de Sang')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .blood-type-A { @apply bg-blue-100 text-blue-800; }
        .blood-type-B { @apply bg-purple-100 text-purple-800; }
        .blood-type-AB { @apply bg-green-100 text-green-800; }
        .blood-type-O { @apply bg-orange-100 text-orange-800; }
        
        .sidebar-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <nav class="w-64 sidebar-gradient shadow-lg">
            <div class="flex flex-col h-full">
                <!-- Logo et utilisateur -->
                <div class="p-6 text-center border-b border-red-500">
                    <div class="flex items-center justify-center mb-4">
                        <i class="fas fa-tint text-white text-2xl mr-2"></i>
                        <h1 class="text-xl font-bold text-white">Blood Bank</h1>
                    </div>
                    <div class="text-white">
                        <p class="text-sm opacity-90">{{ auth()->user()->name }}</p>
                        <span class="inline-block px-2 py-1 mt-1 text-xs bg-white bg-opacity-20 rounded-full">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex-1 px-4 py-6 overflow-y-auto">
                    <ul class="space-y-2">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                <span>Tableau de bord</span>
                            </a>
                        </li>

                        @if(auth()->user()->is_client)
                            <!-- Menu client -->
                            <li class="pt-4">
                                <p class="px-4 text-xs font-semibold text-white opacity-60 uppercase tracking-wider">Donneur</p>
                            </li>
                            <li>
                                <a href="{{ route('appointments.index') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('appointments.*') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-calendar-alt mr-3"></i>
                                    <span>Mes Rendez-vous</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('campaigns.public') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('campaigns.public') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-bullhorn mr-3"></i>
                                    <span>Campagnes</span>
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->is_manager || auth()->user()->is_admin)
                            <!-- Menu Manager -->
                            <li class="pt-4">
                                <p class="px-4 text-xs font-semibold text-white opacity-60 uppercase tracking-wider">Médical</p>
                            </li>
                            <li>
                                <a href="{{ route('patients.index') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('patients.*') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-user-injured mr-3"></i>
                                    <span>Patients</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('transfusions.index') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('transfusions.*') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-syringe mr-3"></i>
                                    <span>Transfusions</span>
                                </a>
                            </li>
                            @php
                                $alertCount = 0;
                                if(auth()->check() && (auth()->user()->is_admin || auth()->user()->is_manager)) {
                                    $alertCount = \App\Models\Alert::where('center_id', auth()->user()->center_id ?? null)
                                        ->where('resolved', false)
                                        ->count();
                                }
                            @endphp
                            <li>
                                <a href="{{ route('blood-bags.stock') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('blood-bags.stock') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-chart-bar mr-3"></i>
                                    <span>Stock de Sang</span>
                                    @if($alertCount > 0)
                                        <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">{{ $alertCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('campagne.index') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('campagne.index') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-bullhorn mr-3"></i>
                                    <span>Campagne de dons</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('dons.index') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('dons.*') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-heart mr-3"></i>
                                    <span>Dons</span>
                                </a>
                            </li>
                        
                            <li>
                                <a href="{{ route('blood-bags.index') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('blood-bags.*') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-tint mr-3"></i>
                                    <span>Poches de Sang</span>
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->is_admin)
                            <!-- Menu Admin -->
                            <li class="pt-4">
                                <p class="px-4 text-xs font-semibold text-white opacity-60 uppercase tracking-wider">Administration</p>
                            </li>
                            <li>
                                <a href="{{ route('users.index') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('users.*') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-users mr-3"></i>
                                    <span>Utilisateurs</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('centers.index') }}" 
                                   class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 {{ request()->routeIs('centers.*') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}">
                                    <i class="fas fa-hospital mr-3"></i>
                                    <span>Centres</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Menu utilisateur -->
                <div class="p-4 border-t border-red-500">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center px-4 py-3 text-white rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10">
                                <i class="fas fa-user-cog mr-3"></i>
                                <span>Profil</span>
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="flex items-center w-full px-4 py-3 text-white rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-semibold text-gray-900">
                            @yield('page-title', 'Tableau de bord')
                        </h1>
                        <div class="flex items-center space-x-4">
                            @yield('page-actions')
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span class="text-green-800">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <span class="text-red-800">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2 mt-1"></i>
                            <div class="text-red-800">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>