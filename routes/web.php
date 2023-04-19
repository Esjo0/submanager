<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeysController;
use App\Http\Controllers\SubscribersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [KeysController::class, 'index'])->name('home');

Route::post('/check_key', [KeysController::class, 'check_key'])->name('check');

Route::get('/subscribers/{id}', [SubscribersController::class, 'index'])->name('subscribers');

Route::post('/create_group', [SubscribersController::class, 'create_group'])->name('create_group');

Route::post('/add_subscriber', [SubscribersController::class, 'store'])->name('add_subscriber');

Route::get('/datatable', [SubscribersController::class, 'list'])->name('datatable');
Route::post('/edit', [SubscribersController::class, 'edit'])->name('edit');
Route::delete('datatable/delete/{id}', [SubscribersController::class, 'delete'])->name('delete');