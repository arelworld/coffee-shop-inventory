<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;

// Simple Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public route - redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Protect all routes
Route::middleware('auth')->group(function () {
    // DASHBOARD - Accessible to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ITEM ROUTES - Staff and Managers can access
    Route::middleware(['auth', 'role:staff,manager'])->group(function () {
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::post('/items/restock', [ItemController::class, 'restock'])->name('items.restock');
        Route::post('/items/record-usage', [ItemController::class, 'recordUsage'])->name('items.record-usage');
    });

    // MANAGER-ONLY ITEM ROUTES
    Route::middleware('role:manager')->group(function () {
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    });

    // SUPPLIER ROUTES - Staff and Managers can access
    Route::middleware(['auth', 'role:staff,manager'])->group(function () {
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    });

    // MANAGER-ONLY SUPPLIER ROUTES
    Route::middleware('role:manager')->group(function () {
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // REPORTS - Manager only
    Route::middleware('role:manager')->prefix('reports')->group(function () {
        Route::get('/inventory/pdf', [DashboardController::class, 'downloadInventoryPDF'])->name('reports.inventory.pdf');
        Route::get('/low-stock/pdf', [DashboardController::class, 'downloadLowStockPDF'])->name('reports.low-stock.pdf');
        Route::get('/expiry/pdf', [DashboardController::class, 'downloadExpiryPDF'])->name('reports.expiry.pdf');
        Route::get('/transactions/pdf', [DashboardController::class, 'downloadTransactionsPDF'])->name('reports.transactions.pdf');
    });

    // EXPORTS - Manager only
    Route::middleware('role:manager')->prefix('exports')->group(function () {
        Route::get('/inventory/csv', [DashboardController::class, 'exportInventoryCSV'])->name('exports.inventory.csv');
        Route::get('/transactions/csv', [DashboardController::class, 'exportTransactionsCSV'])->name('exports.transactions.csv');
        Route::get('/low-stock/csv', [DashboardController::class, 'exportLowStockCSV'])->name('exports.low-stock.csv');
    });

    // USER MANAGEMENT - Manager only
    Route::middleware('role:manager')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::put('/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
        Route::put('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        // Add these to the manager-only user management routes
Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
Route::put('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
});