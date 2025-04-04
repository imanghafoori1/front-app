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
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/products/add', [AdminController::class, 'addProductForm'])->name('admin.add.product');
    Route::post('/admin/products/add', StoreProcustController::class)->name('admin.add.product.submit');
    Route::get('/admin/products/edit/{id}', [AdminController::class, 'editProduct'])->name('admin.edit.product');
    Route::patch('/admin/products/edit/{id}', UpdateProductController::class)->name('admin.update.product');
    Route::get('/admin/products/delete/{id}', [AdminController::class, 'deleteProduct'])->name('admin.delete.product');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
