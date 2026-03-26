<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObligationController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PartnerServiceController;
use App\Http\Controllers\PartnerContactController;
use App\Http\Controllers\CredentialController;

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