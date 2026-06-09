<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Mostafax\AnalyticsSuite\Http\Controllers\AnalyticsController;
use Mostafax\AnalyticsSuite\Http\Controllers\DashboardController;
use Mostafax\AnalyticsSuite\Http\Controllers\ExportController;
use Mostafax\AnalyticsSuite\Http\Controllers\ReportBuilderController;
use Mostafax\AnalyticsSuite\Http\Controllers\ScheduleController;
use Mostafax\AnalyticsSuite\Http\Controllers\WidgetController;

/*
|--------------------------------------------------------------------------
| Enterprise Analytics Suite — API Routes
|--------------------------------------------------------------------------
| Prefix:     api/analytics   (configurable in analytics-suite.routes)
| Middleware: api, auth:sanctum
|--------------------------------------------------------------------------
*/

// ---- Public route (no auth) ---------------------------------------------
Route::get('/public/{token}', [DashboardController::class, 'showPublic'])
    ->name('analytics.dashboard.public');

// ---- Authenticated routes -----------------------------------------------
Route::middleware(config('analytics-suite.routes.middleware', ['api', 'auth:sanctum']))->group(function () {

    // Analytics overview
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('modules',              [AnalyticsController::class, 'modules']);
        Route::get('stats',                [AnalyticsController::class, 'stats']);
        Route::get('summary/{module}',     [AnalyticsController::class, 'summary']);
        Route::get('dashboard/{id}/data',  [AnalyticsController::class, 'dashboardData']);
        Route::post('cache/invalidate',    [AnalyticsController::class, 'invalidateCache']);
    });

    // Dashboards
    Route::apiResource('dashboards', DashboardController::class)
        ->names([
            'index'   => 'analytics.dashboards.index',
            'store'   => 'analytics.dashboards.store',
            'show'    => 'analytics.dashboards.show',
            'update'  => 'analytics.dashboards.update',
            'destroy' => 'analytics.dashboards.destroy',
        ]);

    Route::prefix('dashboards/{id}')->name('analytics.dashboards.')->group(function () {
        Route::post('clone',   [DashboardController::class, 'clone'])->name('clone');
        Route::post('layout',  [DashboardController::class, 'layout'])->name('layout');
        Route::post('share',   [DashboardController::class, 'share'])->name('share');
        Route::delete('share', [DashboardController::class, 'unshare'])->name('unshare');
        Route::get('export',   [DashboardController::class, 'export'])->name('export');
    });

    Route::post('dashboards/import', [DashboardController::class, 'import'])
        ->name('analytics.dashboards.import');

    // Widgets (nested under dashboard)
    Route::prefix('dashboards/{dashboardId}/widgets')->name('analytics.widgets.')->group(function () {
        Route::get('/',        [WidgetController::class, 'index'])->name('index');
        Route::post('/',       [WidgetController::class, 'store'])->name('store');
        Route::get('/{id}',    [WidgetController::class, 'show'])->name('show');
        Route::put('/{id}',    [WidgetController::class, 'update'])->name('update');
        Route::delete('/{id}', [WidgetController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/data',    [WidgetController::class, 'data'])->name('data');
        Route::post('/{id}/refresh',[WidgetController::class, 'refresh'])->name('refresh');
    });

    Route::get('widget-types', [WidgetController::class, 'types'])
        ->name('analytics.widget-types');

    // Report Builder
    Route::prefix('reports')->name('analytics.reports.')->group(function () {
        Route::get('/',             [ReportBuilderController::class, 'index'])->name('index');
        Route::post('/',            [ReportBuilderController::class, 'store'])->name('store');
        Route::post('/run-preview', [ReportBuilderController::class, 'runPreview'])->name('run-preview');
        Route::get('/{id}',         [ReportBuilderController::class, 'show'])->name('show');
        Route::put('/{id}',    [ReportBuilderController::class, 'update'])->name('update');
        Route::delete('/{id}', [ReportBuilderController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/run',    [ReportBuilderController::class, 'run'])->name('run');
        Route::post('/{id}/clone',  [ReportBuilderController::class, 'clone'])->name('clone');
        Route::get('/{id}/export',  [ReportBuilderController::class, 'exportTemplate'])->name('export-template');
        Route::post('/import',      [ReportBuilderController::class, 'importTemplate'])->name('import-template');
    });

    // Exports
    Route::prefix('exports')->name('analytics.export.')->group(function () {
        Route::post('queue',       [ExportController::class, 'queueExport'])->name('queue');
        Route::get('status/{id}',  [ExportController::class, 'status'])->name('status');
        Route::get('formats',      [ExportController::class, 'formats'])->name('formats');
        Route::get('history',      [ExportController::class, 'history'])->name('history');
    });

    // Scheduling
    Route::prefix('schedules')->name('analytics.schedules.')->group(function () {
        Route::get('/',          [ScheduleController::class, 'index'])->name('index');
        Route::post('/',         [ScheduleController::class, 'store'])->name('store');
        Route::get('/{id}',      [ScheduleController::class, 'show'])->name('show');
        Route::put('/{id}',      [ScheduleController::class, 'update'])->name('update');
        Route::delete('/{id}',   [ScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/pause',  [ScheduleController::class, 'pause'])->name('pause');
        Route::post('/{id}/resume', [ScheduleController::class, 'resume'])->name('resume');
    });
});
