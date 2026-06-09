<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Contracts;

use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\DTOs\AnalyticsResultDTO;
use Mostafax\AnalyticsSuite\DTOs\WidgetDataDTO;

interface AnalyticsEngineInterface
{
    public function executeWidget(int|string $widgetId, array $params = []): WidgetDataDTO;

    public function executeDashboard(int|string $dashboardId, array $params = []): Collection;

    public function computeStats(string $model, array $config = []): AnalyticsResultDTO;

    public function getAvailableModules(): Collection;

    public function generateSummary(string $module): AnalyticsResultDTO;

    public function refreshCache(int|string $widgetId): bool;

    public function invalidateDashboardCache(int|string $dashboardId): bool;
}
