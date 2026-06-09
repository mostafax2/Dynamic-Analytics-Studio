<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Providers;

use Illuminate\Cache\CacheManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Mostafax\AnalyticsSuite\Analytics\AnalyticsEngine;
use Mostafax\AnalyticsSuite\AnalyticsSuiteManager;
use Mostafax\AnalyticsSuite\Cache\AnalyticsCacheManager;
use Mostafax\AnalyticsSuite\Commands\DetectModelsCommand;
use Mostafax\AnalyticsSuite\Commands\InstallCommand;
use Mostafax\AnalyticsSuite\Commands\SyncPermissionsCommand;
use Mostafax\AnalyticsSuite\Contracts\AnalyticsEngineInterface;
use Mostafax\AnalyticsSuite\Contracts\DashboardRepositoryInterface;
use Mostafax\AnalyticsSuite\Contracts\DetectionEngineInterface;
use Mostafax\AnalyticsSuite\Contracts\ExportManagerInterface;
use Mostafax\AnalyticsSuite\Contracts\SchedulerInterface;
use Mostafax\AnalyticsSuite\Contracts\SecurityManagerInterface;
use Mostafax\AnalyticsSuite\Contracts\WidgetRepositoryInterface;
use Mostafax\AnalyticsSuite\Detection\ModelDetectionEngine;
use Mostafax\AnalyticsSuite\Export\ExportManager;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Repositories\EloquentDashboardRepository;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Repositories\EloquentWidgetRepository;
use Mostafax\AnalyticsSuite\Jobs\DispatchScheduledReportJob;
use Mostafax\AnalyticsSuite\Scheduling\ReportScheduler;
use Mostafax\AnalyticsSuite\Security\SecurityManager;
use Mostafax\AnalyticsSuite\Services\DashboardService;
use Mostafax\AnalyticsSuite\Services\WidgetService;

class AnalyticsSuiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/analytics-suite.php',
            'analytics-suite'
        );

        $this->registerCacheManager();
        $this->registerRepositories();
        $this->registerServices();
        $this->registerEngine();
        $this->registerSecurity();
        $this->registerExport();
        $this->registerScheduler();
        $this->registerDetection();
        $this->registerManager();
    }

    public function boot(): void
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->publishAssets();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                SyncPermissionsCommand::class,
                DetectModelsCommand::class,
            ]);
        }

        $this->loadRoutes();
        $this->scheduleJobs();
    }

    // -------------------------------------------------------------------------
    // Registration
    // -------------------------------------------------------------------------

    private function registerCacheManager(): void
    {
        $this->app->singleton(AnalyticsCacheManager::class, function ($app) {
            $driver = config('analytics-suite.cache.driver', 'redis');
            /** @var CacheManager $cm */
            $cm = $app->make(CacheManager::class);
            return new AnalyticsCacheManager($cm->driver($driver));
        });
    }

    private function registerRepositories(): void
    {
        $this->app->bind(DashboardRepositoryInterface::class, EloquentDashboardRepository::class);
        $this->app->bind(WidgetRepositoryInterface::class,   EloquentWidgetRepository::class);
    }

    private function registerServices(): void
    {
        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService(
                $app->make(DashboardRepositoryInterface::class),
                $app->make(AnalyticsCacheManager::class),
            );
        });

        $this->app->singleton(WidgetService::class, function ($app) {
            return new WidgetService(
                $app->make(WidgetRepositoryInterface::class),
                $app->make(AnalyticsCacheManager::class),
            );
        });
    }

    private function registerEngine(): void
    {
        $this->app->singleton(AnalyticsEngine::class, function ($app) {
            return new AnalyticsEngine(
                $app->make(\Mostafax\ReportingEngine\Core\Engine\ReportEngine::class),
                $app->make(AnalyticsCacheManager::class),
                $app->make(DetectionEngineInterface::class),
            );
        });

        $this->app->bind(AnalyticsEngineInterface::class, AnalyticsEngine::class);
    }

    private function registerSecurity(): void
    {
        $this->app->singleton(SecurityManager::class, fn () => new SecurityManager());
        $this->app->bind(SecurityManagerInterface::class, SecurityManager::class);
    }

    private function registerExport(): void
    {
        $this->app->singleton(ExportManager::class, fn () => new ExportManager());
        $this->app->bind(ExportManagerInterface::class, ExportManager::class);
    }

    private function registerScheduler(): void
    {
        $this->app->singleton(ReportScheduler::class, function ($app) {
            return new ReportScheduler($app->make(ExportManager::class));
        });
        $this->app->bind(SchedulerInterface::class, ReportScheduler::class);
    }

    private function registerDetection(): void
    {
        $this->app->singleton(ModelDetectionEngine::class, fn () => new ModelDetectionEngine());
        $this->app->bind(DetectionEngineInterface::class, ModelDetectionEngine::class);
    }

    private function registerManager(): void
    {
        $this->app->singleton(AnalyticsSuiteManager::class, function ($app) {
            return new AnalyticsSuiteManager($app);
        });
    }

    // -------------------------------------------------------------------------
    // Publishing
    // -------------------------------------------------------------------------

    private function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/analytics-suite.php' => config_path('analytics-suite.php'),
        ], 'analytics-suite-config');
    }

    private function publishMigrations(): void
    {
        $this->publishes([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'analytics-suite-migrations');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    private function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/../../resources/vue'    => resource_path('js/analytics-suite'),
            __DIR__ . '/../../resources/css'    => resource_path('css/analytics-suite'),
        ], 'analytics-suite-assets');
    }

    // -------------------------------------------------------------------------
    // Routes
    // -------------------------------------------------------------------------

    private function loadRoutes(): void
    {
        $prefix     = config('analytics-suite.routes.prefix', 'api/analytics');
        $middleware = config('analytics-suite.routes.middleware', ['api']);

        $this->app->make('router')
            ->prefix($prefix)
            ->middleware($middleware)
            ->group(__DIR__ . '/../../routes/api.php');
    }

    // -------------------------------------------------------------------------
    // Scheduled Jobs
    // -------------------------------------------------------------------------

    private function scheduleJobs(): void
    {
        if (!config('analytics-suite.scheduling.enabled', true)) {
            return;
        }

        $this->app->booted(function () {
            /** @var Schedule $schedule */
            $schedule = $this->app->make(Schedule::class);

            // Check for due scheduled reports every minute
            $schedule->call(function () {
                /** @var ReportScheduler $scheduler */
                $scheduler = app(ReportScheduler::class);
                $due       = $scheduler->getDueReports();
                foreach ($due as $report) {
                    DispatchScheduledReportJob::dispatch($report->id);
                }
            })->everyMinute()->name('analytics-suite:dispatch-scheduled-reports');
        });
    }
}
