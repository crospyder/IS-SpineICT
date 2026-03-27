<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObligationController;
use App\Http\Controllers\PartnerController;
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

    Route::resource('partners', PartnerController::class);
    Route::resource('partner-services', PartnerServiceController::class);
    Route::resource('obligations', ObligationController::class);

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
});