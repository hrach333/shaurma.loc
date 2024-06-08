<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserDetailsController;


// Главная страница сайта
Route::get('/', [HomeController::class, 'index'])->name('home');

// Маршрут для выхода из системы
Route::get('/logout', [AdminController::class, 'logout']);

// Главная страница для авторизованных и проверенных пользователей
Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Добавление товара в корзину
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add');

// Просмотр корзины
Route::get('/cart', [CartController::class, 'viewCart'])->name('view.cart');

// Удаление товара из корзины
Route::delete('/cart/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

// Получение количества товаров в корзине
Route::get('/cart/count', [CartController::class, 'getItemCount']);

// Обновление количества товара в корзине
Route::post('/cart/updateQuantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
// Форма заказа
Route::get('/order', [OrderController::class, 'showForm'])->name('order.form');
Route::post('/order/create', [OrderController::class, 'create'])->name('order.create');

// Список участников СБП
Route::get('/payment/sbp/participants', [PaymentController::class, 'getSbpParticipants'])->name('payment.sbp.participants');

// Создание платежа
Route::post('/payment/create', [PaymentController::class, 'createPayment'])->name('payment.create');

// Завершение заказа
Route::get('/order/complete', [OrderController::class, 'complete'])->name('order.complete');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/admin/add-admin', [AdminController::class, 'setAdmin']);
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.home');
    Route::get('/user/details', [UserDetailsController::class, 'edit'])->name('user.details');
    Route::post('/user/details', [UserDetailsController::class, 'update'])->name('user.details.update');
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
