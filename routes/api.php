<?php

use App\Http\Controllers\BookedTableController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tables', [TableController::class, 'index']);

    // Запрошення на столик користувача, який в сесії
    Route::get('/booked-tables', [BookedTableController::class, 'index']);

    // Заброньований час на якусь дату
    Route::get('/booked-tables-time/{tableId}', [BookedTableController::class, 'time']);

    // Доступний час на якусь дату
    Route::get('/available-time/{tableId}', [BookedTableController::class, 'availableTime']);

    // Забронювати столик
    Route::post('/booked-tables/{tableId}', [BookedTableController::class, 'store']);


    // Об'єднанні дії
    Route::patch('/booked-tables/{bookedTableId}/{action}', [BookedTableController::class, 'updateStatus']);

    // Відмінити запрошення на столик
    Route::post('/cancel-booked-tables/{bookedTableId}', [BookedTableController::class, 'cancel']);

    // Відхилити запрошення на столик
    Route::post('/decline-booked-tables/{bookedTableId}', [BookedTableController::class, 'decline']);

    // Прийняти запрошення на столик
    Route::post('/accept-booked-tables/{bookedTableId}', [BookedTableController::class, 'accept']);

    // Отримати дані користувача, який в сесії
    Route::get('users/me', [UserController::class, 'show']);

    // Отримання всіх юзерів
    Route::get('users', [UserController::class, 'index']);

    // Загрузити фото юзера
    Route::post('upload-user-photo', [UserController::class, 'uploadPhoto']);

    // Вихід з сесії
    Route::post('auth/logout', [UserController::class, 'logout']);

    Route::get('auth/me', [UserController::class, 'me']);
});


Route::post('auth/login', [UserController::class, 'login']);
Route::post('auth/register', [UserController::class, 'register']);
Route::get('auth/check-token', [UserController::class, 'checkToken']);
