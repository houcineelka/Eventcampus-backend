<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\RoleRequestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;

use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/check-email', [RoleRequestController::class, 'checkEmail']);
Route::post('/role-requests', [RoleRequestController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/logout', [UserController::class, 'logout']);

     // Clubs
    Route::get('/clubs',              [ClubController::class, 'index']);
    Route::get('/clubs/{id}',         [ClubController::class, 'show']);
    Route::post('/clubs/{id}/join',   [ClubController::class, 'join']);
    Route::post('/clubs/{id}/leave',  [ClubController::class, 'leave']);

    // Events
    Route::get('/events',                  [EventController::class, 'index']);
    Route::get('/events/{id}',             [EventController::class, 'show']);
    Route::post('/inscriptions',           [EventController::class, 'inscrire']);
   

    Route::middleware('role:etudiant')->group(function () {
        Route::get('/student/dashboard', [StudentController::class, 'dashboard']);
    });
});
