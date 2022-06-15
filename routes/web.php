<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoxesController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ItemsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('/auth')->group(function() {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/authorize', [AuthController::class, 'authorize'])->name('authorize');
    Route::post('/do_register', [AuthController::class, 'do_register'])->name('do_register');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/', [BoxesController::class, 'index'])->name('index');
Route::get('/boxes/{box}', [BoxesController::class, 'show'])->name('show');
Route::get('/items', [ItemsController::class, 'index'])->name('items');
Route::get('/items/sell/{user_item_id}', [ItemsController::class, 'sellItem'])->name('sellItem');
Route::get('/getBalance', [AuthController::class, 'getBalance'])->name('getBalance');

Route::post('/calculate', [TestController::class, 'index'])->name('calculate');
