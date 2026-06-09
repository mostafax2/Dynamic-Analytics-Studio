<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\Analytics\AnalyticsEngine;
use Mostafax\AnalyticsSuite\Cache\AnalyticsCacheManager;
use Mostafax\AnalyticsSuite\Detection\ModelDetectionEngine;
use Mostafax\AnalyticsSuite\Export\ExportManager;
use Mostafax\AnalyticsSuite\Scheduling\ReportScheduler;
use Mostafax\AnalyticsSuite\Security\SecurityManager;
use Mostafax\AnalyticsSuite\Services\DashboardService;
use Mostafax\AnalyticsSuite\Services\WidgetService;

/**
 * Central entry-point for the Enterprise Analytics Suite.
 *
 * Usage:
 *   AnalyticsSuite::registerWidget(MyWidget::class);
 *   AnalyticsSuite::engine()->executeWidget(1);
 *   AnalyticsSuite::detectModels();
 */
final class AnalyticsSuiteManager
{
    /** @var array<int, string> Custom widget classes registered by host applications */
    private array $registeredWidgets = [];

    public function __construct(private readonly Application $app) {}

    /**
     * Register a custom widget class in the marketplace.
     *
     * Example:
     *   AnalyticsSuite::registerWidget(SalesComparisonWidget::class);
     */
    public function registerWidget(string $class): void
    {
        $this->registeredWidgets[] = $class;

        // Forward registration to the WidgetService repository
        $type = defined("{$class}::TYPE") ? $class::TYPE : class_basename($class);
        $this->widgets()->registerCustomType($type, $class);
    }

    public function getRegisteredWidgets(): array
    {
        return $this->registeredWidgets;
    }

    public function engine(): AnalyticsEngine
    {
        return $this->app->make(AnalyticsEngine::class);
    }

    public function dashboards(): DashboardService
    {
        return $this->app->make(DashboardService::class);
    }

    public function widgets(): WidgetService
    {
        return $this->app->make(WidgetService::class);
    }

    public function scheduler(): ReportScheduler
    {
        return $this->app->make(ReportScheduler::class);
    }

    public function exporter(): ExportManager
    {
        return $this->app->make(ExportManager::class);
    }

    public function detector(): ModelDetectionEngine
    {
        return $this->app->make(ModelDetectionEngine::class);
    }

    public function security(): SecurityManager
    {
        return $this->app->make(SecurityManager::class);
    }

    public function cache(): AnalyticsCacheManager
    {
        return $this->app->make(AnalyticsCacheManager::class);
    }

    public function detectModels(): Collection
    {
        return $this->detector()->detectModels();
    }

    public function version(): string
    {
        return config('analytics-suite.version', '1.0.0');
    }
}
