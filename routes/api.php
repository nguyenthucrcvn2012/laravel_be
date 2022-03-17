<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });


    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::put('users/active/{id}', [UserController::class, 'active'])->name('users.active');

//Route::group([
//    'middleware' => ['api', 'cors']
//], function () {
    Route::post('users/search', [UserController::class, 'search'])->name('users.search');
    Route::post('customers/search', [CustomerController::class, 'search'])->name('customers.search');

    Route::post('customers/import', [CustomerController::class, 'importCsv'])->name('customers.import.csv');
    Route::post('customers/export', [CustomerController::class, 'exportCsv'])->name('customers.export.csv');
//});
