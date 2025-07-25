<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BloodBagController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TransfusionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// Page d'accueil = recherche sang
Route::get('/', [\App\Http\Controllers\SearchBloodController::class, 'search'])->name('home');

// Dashboard principal avec redirection selon le rôle
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes pour les donneurs (pas de dashboard)
Route::middleware(['auth', 'role:donor'])->group(function () {
    Route::resource('appointments', AppointmentController::class)->except(['destroy']);
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'destroy'])->name('appointments.cancel');
});

// Routes pour les patients (pas de dashboard)
Route::middleware(['auth', 'role:patient'])->group(function () {
    // Routes spécifiques aux patients si nécessaire
});

// Routes pour les managers (avec dashboard)
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::resource('campaigns', CampaignController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::get('/blood-bags/stock', [BloodBagController::class, 'stock'])->name('blood-bags.stock');
});

// Routes pour les clients (avec dashboard)
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/campaigns/public', [CampaignController::class, 'upcoming'])->name('campaigns.public');
    Route::get('/blood-bags/available', [BloodBagController::class, 'available'])->name('blood-bags.available');
});

// Routes pour les admins (avec dashboard)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('blood-bags', BloodBagController::class);
    Route::resource('campaigns', CampaignController::class);
    Route::resource('donations', DonationController::class);
    Route::resource('patients', PatientController::class);
    Route::resource('transfusions', TransfusionController::class);
    Route::resource('centers', \App\Http\Controllers\CenterController::class);
    // Routes spéciales pour les rendez-vous (admin peut tout voir/modifier)
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.admin.index');
    Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    // Campagnes à venir (public pour les donneurs)
    Route::get('/campaigns/upcoming', [CampaignController::class, 'upcoming'])->name('campaigns.upcoming');
});

// Routes publiques pour les campagnes (visible par tous les utilisateurs connectés)
Route::middleware('auth')->group(function () {
    Route::get('/campaigns/public', [CampaignController::class, 'upcoming'])->name('campaigns.public');
});

Route::get('/prendre-rendez-vous', [AppointmentController::class, 'publicForm'])->name('appointment.public');
Route::post('/prendre-rendez-vous', [AppointmentController::class, 'publicStore'])->name('appointment.public.store');

Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::post('/reservation', [\App\Http\Controllers\SearchBloodController::class, 'storeReservation'])->name('reservation.store');

// Routes pour la gestion des stocks (admin + manager)
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/blood-bags/stock', [BloodBagController::class, 'stock'])->name('blood-bags.stock');
    Route::post('/blood-bags/mark-expired', [BloodBagController::class, 'markExpired'])->name('blood-bags.markExpired');
});

// Recherche de sang par région et groupe sanguin
Route::get('/recherche-sang', [\App\Http\Controllers\SearchBloodController::class, 'search'])->name('search.blood');

// Recherche AJAX de sang (API)
Route::post('/api/recherche-sang', [\App\Http\Controllers\SearchBloodController::class, 'searchAjax'])->name('api.search.blood');

// API: centres par région
Route::get('/api/centers-by-region/{region}', [\App\Http\Controllers\SearchBloodController::class, 'centersByRegion']);

// Route de test temporaire pour vérifier les rôles
Route::get('/test-roles', function () {
    $users = \App\Models\User::all();
    $roles = [];
    foreach ($users as $user) {
        $roles[] = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'has_dashboard' => $user->has_dashboard,
        ];
    }
    return response()->json($roles);
})->name('test.roles');

require __DIR__.'/auth.php';
