<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\RoleRequestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdhesionController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ProfilController;


use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/check-email', [RoleRequestController::class, 'checkEmail']);
Route::post('/role-requests', [RoleRequestController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::patch('/users/me/preferences', [UserController::class, 'updatePreferences']);

     // Clubs
    Route::post('/clubs',             [ClubController::class, 'store']);
    Route::get('/clubs',              [ClubController::class, 'index']);
    Route::get('/clubs/{id}',         [ClubController::class, 'show']);
    Route::get('/profil/clubs',       [ClubController::class, 'mesClubs']);
    Route::get('/adhesions',                     [AdhesionController::class, 'index']);
    Route::post('/adhesions',                    [AdhesionController::class, 'store']);
    Route::put('/adhesions/{id}/accepter',       [AdhesionController::class, 'accepter']);
    Route::put('/adhesions/{id}/refuser',        [AdhesionController::class, 'refuser']);
    Route::post('/clubs/{id}/leave',             [ClubController::class, 'leave']);

    // Events
    Route::get('/events',                  [EventController::class, 'index']);
    Route::get('/events/{id}',             [EventController::class, 'show']);
    Route::get('/events/{id}/inscrits',    [EventController::class, 'inscrits']);
    Route::delete('/events/{id}',          [EventController::class, 'destroy']);
    Route::get('/organiser/events',        [EventController::class, 'mesEvenements']);
    Route::post('/inscriptions',           [EventController::class, 'inscrire']);
   
    // Inscriptions 
    Route::post('/events/{id}/inscriptions', [InscriptionController::class, 'store']);
    Route::delete('/events/{id}/inscriptions', [InscriptionController::class, 'destroy']);
    //Profil
    Route::get('/profil/inscriptions', [ProfilController::class, 'inscriptions']);

    Route::middleware('role:etudiant')->group(function () {
        Route::get('/student/dashboard', [StudentController::class, 'dashboard']);
    });


});
