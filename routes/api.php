<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CustomerController;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/search', [UserController::class, 'search'])->name('users.search');
    Route::post('/{id}/delete', [UserController::class, 'delete'])->name('users.delete');
    Route::post('/{id}/active', [UserController::class, 'active'])->name('users.active');
    Route::post('/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::post('/store', [UserController::class, 'store'])->name('users.store');
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('users.index');
    Route::get('/{id}', [ProductController::class, 'show'])->name('users.show');
    Route::post('/{id}/delete', [ProductController::class, 'delete'])->name('users.delete');
    Route::post('/{id}/update', [ProductController::class, 'update'])->name('users.update');
    Route::post('/store', [ProductController::class, 'store'])->name('users.store');
    Route::post('/search', [ProductController::class, 'search'])->name('products.search');
});


Route::get('/customers', [UserController::class, 'index'])->name('users.index');
Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
Route::post('/customers/{id}/update', [CustomerController::class, 'update'])->name('customers.update');
Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
Route::post('/customers/import', [CustomerController::class, 'importCsv'])->name('customers.import.csv');
Route::post('/customers/export', [CustomerController::class, 'exportCsv'])->name('customers.export.csv');
Route::post('/customers/search', [CustomerController::class, 'search'])->name('customers.search');


