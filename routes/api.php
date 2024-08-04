<?php

use App\Http\Controllers\BookedTableController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tables', [TableController::class, 'index']);

    // Запрошення на столик користувача, який в сесії
    Route::get('/booked-tables', [BookedTableController::class, 'index']);

    // Заброньована час на якусь дату
    Route::get('/booked-tables-time/{tableId}', [BookedTableController::class, 'time']);

    // Забронювати столик
    Route::post('/booked-tables/{tableId}', [BookedTableController::class, 'store']);

    // Відмінити запрошення на столик
    Route::post('/cancel-booked-tables/{id}', [BookedTableController::class, 'cancel']);

    // Відхилити запрошення на столик
    Route::post('/decline-booked-tables/{id}', [BookedTableController::class, 'decline']);

    // Прийняти запрошення на столик
    Route::post('/accept-booked-tables/{id}', [BookedTableController::class, 'accept']);
    
    // Отримати дані користувача, який в сесії
    Route::get('users/me', [UserController::class, 'show']);

    // Отримання всіх юзерів
    Route::get('users', [UserController::class, 'index']);

    
    // Загрузити фото юзера
    Route::post('upload-user-photo', [UserController::class, 'uploadPhoto']);

    Route::get('auth/me', [UserController::class, 'me']);
    
    // Вихід з сесії
    Route::post('auth/logout', [UserController::class, 'logout']);

});


Route::post('auth/login', [UserController::class, 'login']);
Route::post('auth/register', [UserController::class, 'register']);