<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\Api\OrderDocumentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Routes existantes...
});

// Routes publiques pour la validation des ordonnances
Route::get('/validate-prescription/{number}', [PrescriptionController::class, 'validatePrescription']);
Route::get('/prescription-history/{number}', [PrescriptionController::class, 'getPrescriptionHistory']);

// Routes pour la validation des documents
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders/{order}/validate-documents', [OrderDocumentController::class, 'validate'])
        ->middleware('can:validate-documents');
    Route::post('/orders/{order}/reject-documents', [OrderDocumentController::class, 'reject'])
        ->middleware('can:validate-documents');
});
