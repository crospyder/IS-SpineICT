<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObligationController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PartnerDocumentController;
use App\Http\Controllers\PartnerServiceController;
use App\Http\Controllers\PartnerContactController;
use App\Http\Controllers\CredentialController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/partners/lookup-by-oib', [PartnerController::class, 'lookupByOib'])
        ->name('partners.lookup-by-oib');

    Route::post('/partners/{partner}/refresh-from-sudreg', [PartnerController::class, 'refreshFromSudreg'])
        ->name('partners.refresh-from-sudreg');

    Route::resource('partners', PartnerController::class);
    Route::resource('partner-services', PartnerServiceController::class);
    Route::resource('obligations', ObligationController::class);

    Route::get('/partner-documents/create', [PartnerDocumentController::class, 'create'])
        ->name('partner-documents.create');
    Route::post('/partner-documents', [PartnerDocumentController::class, 'store'])
        ->name('partner-documents.store');
    Route::get('/partner-documents/{partnerDocument}/view', [PartnerDocumentController::class, 'view'])
        ->name('partner-documents.view');
    Route::get('/partner-documents/{partnerDocument}/download', [PartnerDocumentController::class, 'download'])
        ->name('partner-documents.download');
    Route::delete('/partner-documents/{partnerDocument}', [PartnerDocumentController::class, 'destroy'])
        ->name('partner-documents.destroy');

    Route::patch('/obligations/{obligation}/complete', [ObligationController::class, 'complete'])
        ->name('obligations.complete');

    Route::resource('partner-contacts', PartnerContactController::class)->except(['index', 'show']);
    Route::resource('credentials', CredentialController::class)->except(['index', 'show']);

    Route::post('/credentials/{credential}/reveal', [CredentialController::class, 'reveal'])
        ->name('credentials.reveal');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::resource('procurements', \App\Http\Controllers\ProcurementController::class);
    Route::post('/procurements/{procurement}/items', [\App\Http\Controllers\ProcurementController::class, 'storeItem'])
        ->name('procurements.items.store');

    Route::put('/procurements/{procurement}/items/{procurementItem}', [\App\Http\Controllers\ProcurementController::class, 'updateItem'])
        ->name('procurements.items.update');

    Route::delete('/procurements/{procurement}/items/{procurementItem}', [\App\Http\Controllers\ProcurementController::class, 'destroyItem'])
        ->name('procurements.items.destroy');

    Route::post('/procurements/{procurement}/costs', [\App\Http\Controllers\ProcurementController::class, 'storeCost'])
        ->name('procurements.costs.store');

    Route::put('/procurements/{procurement}/costs/{procurementCost}', [\App\Http\Controllers\ProcurementController::class, 'updateCost'])
        ->name('procurements.costs.update');

    Route::delete('/procurements/{procurement}/costs/{procurementCost}', [\App\Http\Controllers\ProcurementController::class, 'destroyCost'])
        ->name('procurements.costs.destroy');
});