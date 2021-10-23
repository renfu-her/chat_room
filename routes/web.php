<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MessageController;

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

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function (){
        return redirect(route('room.index'));
    });
    # Room
    Route::group(['middleware' => 'auth', 'prefix' => 'room', 'as' => 'room.'], function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/event', [RoomController::class, 'event'])->name('event');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::get('{id}', [RoomController::class, 'chat'])->name('chat');
        Route::post('/store', [RoomController::class, 'store'])->name('store');
        Route::post('/{id}/join', [RoomController::class, 'join'])->name('join');
        Route::put('/{id}/update', [RoomController::class, 'update'])->name('update');
        Route::put('/{id}/join', [RoomController::class, 'joinRoom'])->name('join.room');
        # Message
        Route::post('/{id}/message', [MessageController::class, 'store'])->name('message');
    });
});

