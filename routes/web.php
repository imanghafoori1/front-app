<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\StoreProcustController;
use App\Http\Controllers\Admin\UpdateProductController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');

Route::get('/products/{product_id}', [ProductController::class, 'show'])->name('products.show');

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware(['auth'])->group(function () {
    Route::name('admin.')->prefix('/admin/products')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('products');
        Route::view('/add', 'admin.add_product')->name('add.product');
        Route::post('/add', StoreProcustController::class)->name('add.product.submit');
        Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('edit.product');
        Route::patch('/edit/{id}', UpdateProductController::class)->name('update.product');
        Route::get('/delete/{id}', [AdminController::class, 'delete'])->name('delete.product');
    });
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
