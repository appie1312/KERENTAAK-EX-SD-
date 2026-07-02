<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/registreren', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/registreren', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/inloggen', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/inloggen', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/producten', [ProductController::class, 'index'])->name('products.index');
    Route::get('/producten/toevoegen', [ProductController::class, 'create'])->name('products.create');
    Route::post('/producten', [ProductController::class, 'store'])->name('products.store');
    Route::get('/producten/{product}/wijzigen', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/producten/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/producten/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/profiel', function () {
        return view('profile.show');
    })->name('profile');
    Route::post('/uitloggen', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
