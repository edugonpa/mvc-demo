<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de productos con protección por roles
Route::middleware(['auth'])->group(function () {
    // Listar productos - Usuarios autenticados (admin y user)
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');
    
    // Crear productos - Usuarios autenticados (admin y user)
    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');
    
    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');
    
    // Editar productos - Usuarios autenticados (admin y user)
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');
    
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');
    
    // Eliminar productos - SOLO ADMIN
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('products.destroy');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


