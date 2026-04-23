<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SuperadminController;
use App\Http\Controllers\Api\EventProviderController;
use App\Http\Controllers\Api\POSController;
use App\Http\Controllers\Api\GateController;

use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Superadmin Routes
 */
Route::middleware(['auth:sanctum', 'role:Superadmin'])->group(function () {
    Route::get('/superadmin/tenants', [SuperadminController::class, 'listTenants']);
    Route::post('/superadmin/tenants', [SuperadminController::class, 'createTenant']);
    Route::patch('/superadmin/tenants/{tenant}/status', [SuperadminController::class, 'updateTenantStatus']);
    Route::get('/superadmin/infra/health', [SuperadminController::class, 'getInfrasHealth']);
    Route::post('/superadmin/transactions/{transaction}/override', [SuperadminController::class, 'overrideTransaction']);
});

/**
 * Event Provider Routes
 */
Route::middleware(['auth:sanctum', 'role:Penyedia Event'])->group(function () {
    Route::post('/events', [EventProviderController::class, 'storeEvent']);
    Route::post('/events/{event}/ticket-categories', [EventProviderController::class, 'storeTicketCategory']);
    Route::post('/ticket-categories/{category}/update-design', [EventProviderController::class, 'updateTicketDesign']);
    Route::get('/events/{event}/analytics', [EventProviderController::class, 'getAnalytics']);
    
    // Penjualan & Redemption for Event Provider
    Route::post('/pos/events/{event}/sell', [POSController::class, 'sellTicket']);
    Route::post('/pos/redeem', [POSController::class, 'redeemTicket']);
});

/**
 * POS (Petugas Loket) Routes
 */
Route::middleware(['auth:sanctum', 'role:Petugas Loket'])->group(function () {
    // Moved to Penyedia Event group
});

/**
 * Gate (Petugas Gate) Routes
 */
Route::middleware(['auth:sanctum', 'role:Petugas Gate'])->group(function () {
    Route::post('/gate/scan', [GateController::class, 'scan']);
    Route::post('/gate/sync', [GateController::class, 'syncLogs']);
});
