<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\VoucherController;

Route::group(['prefix' => 'v1', 'namespace' => 'use App\Http\Controllers\Api\V1'], function() {
  Route::post('/login', [LoginController::class, 'login']);
  Route::post('/users', [UserController::class, 'createUser'])->name('users.createUser');

  Route::group(['middleware' => ['verifyToken', 'verifyManager']], function() {
    Route::get('/users', [UserController::class, 'getAllUsers'])->name('users.getAllUsers');
    Route::put('/users/{id}', [UserController::class, 'updateUser'])->name('users.updateUser');
    Route::delete('/users/{id}', [UserController::class, 'deleteUser'])->name('users.deleteUser');

    Route::post('/products', [ProductController::class, 'createProduct'])->name('products.createProduct');
    Route::put('/products/{id}', [ProductController::class, 'updateProduct'])->name('products.updateProduct');
    Route::delete('/products/{id}', [ProductController::class, 'deleteProduct'])->name('products.deleteProduct');

    Route::get('/vouchers', [VoucherController::class, 'getAllVouchers'])->name('vouchers.getAllVouchers');
    Route::get('/vouchers/active', [VoucherController::class, 'getAllActiveVouchers'])->name('vouchers.getAllActiveVouchers');
    Route::get('/vouchers/used', [VoucherController::class, 'getAllUsedVouchers'])->name('vouchers.getAllUsedVouchers');
  });

  Route::group(['middleware' => ['verifyToken']], function() {  
    Route::get('/users/{id}', [UserController::class, 'getUser'])->name('users.getUser');
    Route::put('/users/{id}/points', [UserController::class, 'addPoints'])->name('users.addPoints');
    
    Route::get('/products', [ProductController::class, 'getAllProducts'])->name('products.getAllProducts');
    Route::get('/products/active', [ProductController::class, 'getAllActiveProducts'])->name('products.getAllActiveProducts');
    Route::get('/products/{id}', [ProductController::class, 'getProduct'])->name('products.getProduct');

    Route::post('/vouchers/generate', [VoucherController::class, 'generateVoucher'])->name('vouchers.generateVoucher');
    Route::get('/vouchers/{id}', [VoucherController::class, 'getUserVouchers'])->name('vouchers.getUserVouchers');
    Route::put('/vouchers/use', [VoucherController::class, 'useVoucher'])->name('vouchers.useVoucher');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
  });
});
