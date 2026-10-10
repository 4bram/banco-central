<?php

use App\Http\Controllers\Admin\CashController;
use App\Http\Controllers\Admin\NodeController;
use App\Http\Controllers\Api\NodeController as ApiNodeController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\BalanceController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

// ─── Health ───────────────────────────────────────────
Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'node'   => env('NODE_ID', 'banco-central'),
    'time'   => now(),
]));

// ─── Admin (panel del banco central) ──────────────────
Route::prefix('admin')->group(function () {
    Route::get('/nodes',              [NodeController::class, 'index']);
    Route::post('/nodes',             [NodeController::class, 'store']);
    Route::post('/nodes/{nodeId}/cash', [CashController::class, 'adjust']);
});

// ─── API para nodos autenticados ──────────────────────
Route::middleware('api.key')->group(function () {
    Route::get('/nodes/me', [ApiNodeController::class, 'me']);
    Route::get('/balance/{accountNumber}', [BalanceController::class, 'show']);
});

// Solo sucursales
Route::middleware('api.key:branch')->group(function () {
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::post('/transfer', [TransactionController::class, 'transfer']);
});

// Solo cajeros
Route::middleware('api.key:atm')->group(function () {
    Route::post('/withdraw', [TransactionController::class, 'withdraw']);
    Route::post('/deposit',  [TransactionController::class, 'deposit']);
});
