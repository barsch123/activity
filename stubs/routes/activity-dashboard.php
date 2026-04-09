<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityDashboardController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('app/logs')->group(function () {
        // API endpoints
        Route::get('/api/list', [ActivityDashboardController::class, 'apiList'])->name('activity.api.list');
        Route::get('/api/event/{event}', [ActivityDashboardController::class, 'filterByEvent'])->name('activity.api.filterByEvent');
        Route::get('/api/subject/{subjectType}', [ActivityDashboardController::class, 'filterBySubject'])->name('activity.api.filterBySubject');

        // Dashboard views
        Route::get('/', [ActivityDashboardController::class, 'index'])->name('activity.index');
        Route::get('/{activity}', [ActivityDashboardController::class, 'show'])->name('activity.show');

        Route::delete('/{activity}', [ActivityDashboardController::class, 'delete'])->name('activity.delete');
    });
});
