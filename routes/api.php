<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\TypeAlerteController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\NotificationApiController;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

// Auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::patch('/auth/me', [AuthController::class, 'updateMe']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Groupes par acteur
    Route::prefix('citoyen')->middleware('role:citoyen,super_admin')->group(function (): void {
        Route::get('/alertes', [AlerteController::class, 'index']);
        Route::post('/alertes', [AlerteController::class, 'store']);
        Route::get('/alertes/{alerte}', [AlerteController::class, 'show']);
        Route::get('/stats', [StatsController::class, 'citoyen']);
    });

    Route::prefix('gestionnaire')->middleware('role:gestionnaire,super_admin')->group(function (): void {
        Route::get('/alertes', [AlerteController::class, 'index']);
        Route::get('/alertes/{alerte}', [AlerteController::class, 'show']);
        Route::patch('/alertes/{alerte}', [AlerteController::class, 'update']);
        Route::post('/alertes/{alerte}/statut', [AlerteController::class, 'changerStatut']);
        Route::post('/alertes/{alerte}/approuver', [AlerteController::class, 'approuverResolution']);
        Route::get('/stats', [StatsController::class, 'gestionnaire']);
    });

    Route::prefix('direction')->middleware('role:direction,super_admin')->group(function (): void {
        Route::get('/alertes', [AlerteController::class, 'index']);
        Route::get('/alertes/{alerte}', [AlerteController::class, 'show']);
        Route::patch('/alertes/{alerte}', [AlerteController::class, 'update']);
        Route::post('/alertes/{alerte}/statut', [AlerteController::class, 'changerStatut']);
        Route::get('/stats', [StatsController::class, 'direction']);
    });

    Route::prefix('admin')->middleware('role:super_admin')->group(function (): void {
        Route::get('/stats', [StatsController::class, 'admin']);
        Route::get('/directions', [DirectionController::class, 'index']);
        Route::post('/directions', [DirectionController::class, 'store']);
        Route::put('/directions/{direction}', [DirectionController::class, 'update']);
        Route::delete('/directions/{direction}', [DirectionController::class, 'destroy']);

        Route::get('/types-alertes', [TypeAlerteController::class, 'index']);
        Route::post('/types-alertes', [TypeAlerteController::class, 'store']);
        Route::put('/types-alertes/{type}', [TypeAlerteController::class, 'update']);
        Route::delete('/types-alertes/{type}', [TypeAlerteController::class, 'destroy']);
    });

    // Référentiels accessibles à tous les utilisateurs authentifiés
    Route::get('/directions', [DirectionController::class, 'index']);
    Route::get('/types-alertes', [TypeAlerteController::class, 'index']);

    // Notifications utilisateur (in-app)
    Route::get('/notifications', [NotificationApiController::class, 'index']);
    Route::patch('/notifications/{notification}', [NotificationApiController::class, 'update']);
});


