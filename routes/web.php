<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDirectionController;
use App\Http\Controllers\AdminTypeAlerteController;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminExportController;
use App\Http\Controllers\AdminAlerteController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Auth web (session)
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

Route::middleware(['auth','admin'])->group(function (): void {
    Route::get('/admin', [AdminController::class, 'dashboard']);

    Route::get('/admin/directions', [AdminDirectionController::class, 'index'])->name('admin.directions');
    Route::get('/admin/directions/create', [AdminDirectionController::class, 'create'])->name('admin.directions.create');
    Route::get('/admin/directions/{direction}/edit', [AdminDirectionController::class, 'edit'])->name('admin.directions.edit');
    Route::post('/admin/directions', [AdminDirectionController::class, 'store'])->name('admin.directions.store');
    Route::put('/admin/directions/{direction}', [AdminDirectionController::class, 'update'])->name('admin.directions.update');
    Route::delete('/admin/directions/{direction}', [AdminDirectionController::class, 'destroy'])->name('admin.directions.destroy');

    Route::get('/admin/types', [AdminTypeAlerteController::class, 'index'])->name('admin.types');
    Route::get('/admin/types/create', [AdminTypeAlerteController::class, 'create'])->name('admin.types.create');
    Route::get('/admin/types/{type}/edit', [AdminTypeAlerteController::class, 'edit'])->name('admin.types.edit');
    Route::post('/admin/types', [AdminTypeAlerteController::class, 'store'])->name('admin.types.store');
    Route::put('/admin/types/{type}', [AdminTypeAlerteController::class, 'update'])->name('admin.types.update');
    Route::delete('/admin/types/{type}', [AdminTypeAlerteController::class, 'destroy'])->name('admin.types.destroy');

    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/admin/gestionnaires', [AdminUserController::class, 'gestionnaires'])->name('admin.gestionnaires');
    Route::get('/admin/gestionnaires/create', [AdminUserController::class, 'createGestionnaire'])->name('admin.gestionnaires.create');
    Route::get('/admin/export/alertes.csv', [AdminExportController::class, 'alertesCsv'])->name('admin.export.alertes');

    Route::get('/admin/alertes', [AdminAlerteController::class, 'index'])->name('admin.alertes');
    Route::get('/admin/alertes/{alerte}', [AdminAlerteController::class, 'show'])->name('admin.alertes.show');
    Route::put('/admin/alertes/{alerte}', [AdminAlerteController::class, 'update'])->name('admin.alertes.update');

    Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications');
    Route::post('/admin/notifications', [AdminNotificationController::class, 'store'])->name('admin.notifications.store');
    Route::put('/admin/notifications/{notification}', [AdminNotificationController::class, 'update'])->name('admin.notifications.update');
    Route::delete('/admin/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');

    Route::get('/admin/profiles', [AdminProfileController::class, 'index'])->name('admin.profiles');
    Route::get('/admin/profiles/create', [AdminProfileController::class, 'create'])->name('admin.profiles.create');
    Route::get('/admin/profiles/{profile}/edit', [AdminProfileController::class, 'edit'])->name('admin.profiles.edit');
    Route::post('/admin/profiles', [AdminProfileController::class, 'store'])->name('admin.profiles.store');
    Route::put('/admin/profiles/{profile}', [AdminProfileController::class, 'update'])->name('admin.profiles.update');
    Route::delete('/admin/profiles/{profile}', [AdminProfileController::class, 'destroy'])->name('admin.profiles.destroy');
});
