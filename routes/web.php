<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\MenuManagementController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\LaporanController;




Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // ADMIN ONLY
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

        Route::resource('admin/menu', MenuManagementController::class);

        Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
        Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');
        Route::get('/admin/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
        Route::resource('admin/stok', IngredientController::class)->names('admin.stok');
    });

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // KASIR
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::get('/kasir/riwayat', [KasirController::class, 'riwayat'])->name('kasir.riwayat');
    Route::post('/kasir', [KasirController::class, 'store'])->name('kasir.store');
    Route::get('/kasir/struk/{order}', [KasirController::class, 'struk'])
        ->name('kasir.struk');
    Route::get('/kasir/ringkasan', [KasirController::class, 'ringkasan'])->name('kasir.ringkasan');
    Route::get('/kasir/manajemen-kas', [KasirController::class, 'manajemenKas'])->name('kasir.manajemen-kas');
    Route::post('/kasir/manajemen-kas', [KasirController::class, 'storeKas'])->name('kasir.store-kas');
    Route::get('/kasir/stok', [KasirController::class, 'stok'])->name('kasir.stok');
    Route::put('/kasir/stok/{ingredient}', [KasirController::class, 'updateStok'])->name('kasir.updateStok');
});


require __DIR__ . '/auth.php';