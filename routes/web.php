<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware(['auth', 'it'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/account/password', [UserController::class, 'changePassword'])->name('password.edit');
    Route::put('/account/password', [UserController::class, 'updatePassword'])->name('password.update');
    Route::get('/assets/scan/{barcode}', [AssetController::class, 'scan'])->name('assets.scan');
    Route::get('/assets/{asset}/barcode', [AssetController::class, 'barcode'])->name('assets.barcode');
    Route::resource('assets', AssetController::class);
    Route::middleware('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::middleware('super-admin')->put('/users/{user}/password', [UserController::class, 'forceReset'])->name('users.password.update');
        Route::resource('divisions', DivisionController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('statuses', StatusController::class)->except(['show']);
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::middleware('super-admin')->group(function () {
            Route::put('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
            Route::put('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
        });
    });
});
