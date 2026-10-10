<?php

use App\Http\Controllers\Admin\AccountWebController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CashController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NodeWebController;
use App\Http\Controllers\Admin\ReportWebController;
use App\Http\Controllers\Admin\TransactionWebController;
use App\Http\Controllers\Admin\CashMovementWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

Route::get('/docs', fn () => view('docs'));

// Auth
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Panel protegido
Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/nodes', [NodeWebController::class, 'index'])->name('nodes.index');
    Route::get('/nodes/create', [NodeWebController::class, 'create'])->name('nodes.create');
    Route::post('/nodes', [NodeWebController::class, 'store'])->name('nodes.store');
    Route::get('/nodes/{id}', [NodeWebController::class, 'show'])->name('nodes.show');
    Route::post('/nodes/{nodeId}/cash', [CashController::class, 'adjust'])->name('nodes.cash');

    Route::get('/accounts', [AccountWebController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/{id}', [AccountWebController::class, 'show'])->name('accounts.show');

    Route::get('/transactions', [TransactionWebController::class, 'index'])->name('transactions.index');

    Route::get('/reports', [ReportWebController::class, 'index'])->name('reports.index');

    Route::get('/cash-movements', [CashMovementWebController::class, 'index'])->name('cash.index');

    Route::get('/transactions/export', [TransactionWebController::class, 'export'])->name('transactions.export');
    Route::get('/cash-movements/export', [CashMovementWebController::class, 'export'])->name('cash.export');
});