<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Mostafax\AnalyticsSuite\AnalyticsSuiteManager;

/**
 * @method static void registerWidget(string $class)
 * @method static array getRegisteredWidgets()
 * @method static \Mostafax\AnalyticsSuite\Analytics\AnalyticsEngine engine()
 * @method static \Mostafax\AnalyticsSuite\Services\DashboardService dashboards()
 * @method static \Mostafax\AnalyticsSuite\Services\WidgetService widgets()
 * @method static \Mostafax\AnalyticsSuite\Scheduling\ReportScheduler scheduler()
 * @method static \Mostafax\AnalyticsSuite\Export\ExportManager exporter()
 * @method static \Mostafax\AnalyticsSuite\Detection\ModelDetectionEngine detector()
 * @method static \Mostafax\AnalyticsSuite\Security\SecurityManager security()
 * @method static \Illuminate\Support\Collection detectModels()
 *
 * @see \Mostafax\AnalyticsSuite\AnalyticsSuiteManager
 */
class AnalyticsSuite extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AnalyticsSuiteManager::class;
    }
}
