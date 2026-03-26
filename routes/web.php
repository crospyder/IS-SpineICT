<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObligationController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PartnerServiceController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('partners', PartnerController::class);
Route::resource('partner-services', PartnerServiceController::class);
Route::resource('obligations', ObligationController::class);