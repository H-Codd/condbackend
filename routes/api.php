<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\FoundAndLostController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;

// Teste
Route::get('/ping', function(){
    return ['pong' =>true];
});

// 401
Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

// Auth
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function(){

    // Auth::Validator
    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Wall
    Route::get('/walls', [WallController::class, 'getAll']);
    Route::post('/wall/{id}/like', [WallController::class, 'toggleLike']);

    // Documents
    Route::get('/docs', [DocController::class, 'getAll']);

    // Warnings
    Route::get('/warnings', [WallController::class, 'getMyWarnings']);
    Route::post('/warning', [WallController::class, 'setWarning']);
    Route::post('/warning/file', [WallController::class, 'addWarningFile']);

    // Boletos
    Route::get('/billets', [BilletController::class, 'getAll']);

    // FoundandLost
    Route::get('/foundandlost', [FoundAndLostController::class, 'getAll']);
    Route::post('/foundandlost', [FoundAndLostController::class, 'insert']);
    Route::put('/foundandlost/{id}', [FoundAndLostController::class, 'update']);

    // Unit
    Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
    Route::post('/unit/{id}/addperson', [UnitController::class, 'addPerson']);
    Route::post('/unit/{id}/addvehicle', [UnitController::class, 'addVehicle']);
    Route::post('/unit/{id}/addpet', [UnitController::class, 'addPet']);
    Route::post('/unit/{id}/removeperson', [UnitController::class, 'removePerson']);
    Route::post('/unit/{id}/removevehicle', [UnitController::class, 'removeVehicle']);
    Route::post('/unit/{id}/removepet', [UnitController::class, 'removePet']);
    
    // Reservation
    Route::get('/reservations', [ReservationController::class, 'getReservations']);
    Route::post('/reservations/{id}', [ReservationController::class, 'setReservations']);

    Route::get('/reservation/{id}/disableddates', [ReservationController::class, 'getDisabledDates']);
    Route::get('/reservation/{id}/time', [ReservationController::class, 'getTimes']);

    Route::get('/myreservation', [ReservationController::class, 'getMyReservation']);
    Route::delete('/myreservation/{id}', [ReservationController::class, 'deleteReservation']);
});