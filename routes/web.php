<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlackListController;

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

Route::group(['as' => 'blacklist.'], function() {
    Route::get('/', [BlackListController::class, 'addingForm'])->name('form');
    Route::post('/blacklist/store', [BlackListController::class, 'store'])->name('store');
    Route::get('/example', [BlackListController::class, 'example'])->name('example');
});
