<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CustomerController;

Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
        Route::post('/search', [UserController::class, 'search'])->name('users.search');
        Route::post('/{id}/delete', [UserController::class, 'delete'])->name('users.delete');
        Route::post('/{id}/active', [UserController::class, 'active'])->name('users.active');
        Route::post('/{id}/update', [UserController::class, 'update'])->name('users.update');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
    });

    Route::get('products/', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::post('products/{id}/delete', [ProductController::class, 'delete'])->name('products.delete');
    Route::post('products/{id}/update', [ProductController::class, 'update'])->name('products.update');
    Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
    Route::post('products/search', [ProductController::class, 'search'])->name('products.search');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers/{id}/update', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
    Route::post('/customers/import', [CustomerController::class, 'importCsv'])->name('customers.import.csv');
    Route::post('/customers/export', [CustomerController::class, 'exportCsv'])->name('customers.export.csv');
    Route::post('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

});
