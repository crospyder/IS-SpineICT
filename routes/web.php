<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PartnerServiceController;
use App\Http\Controllers\ObligationController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('partners', PartnerController::class);
Route::resource('partner-services', PartnerServiceController::class);
Route::resource('obligations', ObligationController::class);