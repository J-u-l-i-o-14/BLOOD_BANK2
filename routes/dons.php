<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonsController;

Route::middleware(['auth'])->prefix('dons')->name('dons.')->group(function () {
    Route::get('/', [DonsController::class, 'index'])->name('index');
    Route::get('/create', [DonsController::class, 'create'])->name('create');
    Route::post('/store', [DonsController::class, 'store'])->name('store');

    Route::get('/campagne', [DonsController::class, 'campagne'])->name('campagne');
    Route::post('/campagne/store', [DonsController::class, 'campagneStore'])->name('campagne.store');

    Route::get('/rendezvous', [DonsController::class, 'rendezvous'])->name('rendezvous');
    Route::post('/rendezvous/{appointment}/confirmer', [DonsController::class, 'confirmer'])->name('rendezvous.confirmer');
    Route::post('/rendezvous/{appointment}/annuler', [DonsController::class, 'annuler'])->name('rendezvous.annuler');

    // Inscription des donneurs
    Route::get('/inscription', [DonsController::class, 'inscription'])->name('inscription');
    Route::post('/inscription', [DonsController::class, 'inscriptionStore'])->name('inscription.store');

    // Historique des dons
    Route::get('/historique', [DonsController::class, 'historique'])->name('historique');

    // Liste des donneurs
    Route::get('/liste-donneurs', [DonsController::class, 'listeDonneurs'])->name('liste_donneurs');
});
