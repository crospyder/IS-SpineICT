<?php

use App\Http\Controllers\Api\AgentInventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/agent/inventory-sync', [AgentInventoryController::class, 'sync']);

Route::match(['get', 'post'], '/agent/ping', function (Request $request) {
    return response()->json([
        'ok' => true,
        'method' => $request->method(),
        'host' => $request->getHost(),
        'path' => $request->path(),
        'full_url' => $request->fullUrl(),
    ]);
});