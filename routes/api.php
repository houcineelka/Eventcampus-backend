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
use App\Http\Controllers\MessageController;


use Illuminate\Http\Request;
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
    Route::get('/organiser/clubs',    [ClubController::class, 'mesClubsCreated']);
    Route::get('/adhesions',                     [AdhesionController::class, 'index']);
    Route::post('/adhesions',                    [AdhesionController::class, 'store']);
    Route::put('/adhesions/{id}/accepter',       [AdhesionController::class, 'accepter']);
    Route::put('/adhesions/{id}/refuser',        [AdhesionController::class, 'refuser']);
    Route::post('/clubs/{id}/leave',             [ClubController::class, 'leave']);
    Route::get('/clubs/{id}/messages',           [MessageController::class, 'index']);
    Route::post('/clubs/{id}/messages',          [MessageController::class, 'store']);

    // Conversations & Messages
    Route::prefix('messages')->group(function () {
        Route::get('/conversations', [MessageController::class, 'getConversations']);
        Route::post('/conversations', [MessageController::class, 'createConversation']);
        Route::get('/conversations/{conversationId}', [MessageController::class, 'getMessages']);
        Route::post('/conversations/{conversationId}/send', [MessageController::class, 'sendMessage']);
        Route::post('/broadcast', [MessageController::class, 'sendBroadcastMessage']);
        Route::post('/start/{member}', [MessageController::class, 'startIndividualConversation']);
        Route::get('/received', [MessageController::class, 'getReceivedMessages']);
        Route::put('/{messageId}/read', [MessageController::class, 'markMessageAsRead']);
        Route::get('/search-members', [MessageController::class, 'searchMembers']);
    });

    // Events
    Route::post('/events',                 [EventController::class, 'store']);
    Route::get('/events',                  [EventController::class, 'index']);
    Route::get('/events/{id}',             [EventController::class, 'show']);
    Route::put('/events/{id}',             [EventController::class, 'update']);
    Route::get('/events/{id}/inscrits',    [EventController::class, 'inscrits']);
    Route::delete('/events/{id}',          [EventController::class, 'destroy']);
    Route::get('/organiser/events',        [EventController::class, 'mesEvenements']);
    Route::post('/inscriptions',           [EventController::class, 'inscrire']);
   
    // Inscriptions 
    Route::post('/events/{id}/inscriptions', [InscriptionController::class, 'store']);
    Route::delete('/events/{id}/inscriptions', [InscriptionController::class, 'destroy']);
    //Profil
    Route::get('/profil/inscriptions', [ProfilController::class, 'inscriptions']);
    Route::get('/messages', [MessageController::class, 'userMessages']);
    Route::get('/notifications', function (Request $request) {
        return response()->json($request->user()->notifications);
    });

    Route::middleware('role:etudiant')->group(function () {
        Route::get('/student/dashboard', [StudentController::class, 'dashboard']);
    });


});
