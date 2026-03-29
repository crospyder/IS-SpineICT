<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObligationController;
use App\Http\Controllers\PartnerContactController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PartnerServiceController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;

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
    Route::resource('users', UserController::class)->except(['show', 'destroy']);

    Route::patch('/obligations/{obligation}/complete', [ObligationController::class, 'complete'])
        ->name('obligations.complete');

    Route::resource('partner-contacts', PartnerContactController::class)->except(['index', 'show']);
    Route::resource('credentials', CredentialController::class)->except(['index', 'show']);

    Route::post('/credentials/{credential}/reveal', [CredentialController::class, 'reveal'])
        ->name('credentials.reveal');

    Route::resource('procurements', ProcurementController::class);

    Route::post('/procurements/{procurement}/items', [ProcurementController::class, 'storeItem'])
        ->name('procurements.items.store');

    Route::put('/procurements/{procurement}/items/{procurementItem}', [ProcurementController::class, 'updateItem'])
        ->name('procurements.items.update');

    Route::delete('/procurements/{procurement}/items/{procurementItem}', [ProcurementController::class, 'destroyItem'])
        ->name('procurements.items.destroy');

    Route::post('/procurements/{procurement}/costs', [ProcurementController::class, 'storeCost'])
        ->name('procurements.costs.store');

    Route::put('/procurements/{procurement}/costs/{procurementCost}', [ProcurementController::class, 'updateCost'])
        ->name('procurements.costs.update');

    Route::delete('/procurements/{procurement}/costs/{procurementCost}', [ProcurementController::class, 'destroyCost'])
        ->name('procurements.costs.destroy');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');
Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
});