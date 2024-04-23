<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/logout', [AdminController::class, 'logout']);
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/admin/add-admin', [AdminController::class, 'setAdmin']);
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.home');
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    //Route::get('/admin', [AdminController::class, 'index']);
    Route::get('/admin/produc', [ProductController::class, 'index'])->name('admin.produc');
    Route::post('/admin/save_content', [ProductController::class, 'saveProduct']);
    Route::post('/admin/createProduct', [ProductController::class, 'createProduct'])->name('admin.createProduct');
    Route::post('/admin/save-image', [ProductController::class, 'updateImage'])->name('save.image');

    //Route::get('/admin/products/page/{page}', [ProductController::class, 'index'])->name('admin.products.page');
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.product');
    Route::delete('/admin/products/{id}', [ProductController::class, 'deleteProduct'])->name('products.delete');
});

require __DIR__ . '/auth.php';