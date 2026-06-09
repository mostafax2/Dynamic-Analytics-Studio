<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Analytics;

use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\Cache\AnalyticsCacheManager;
use Mostafax\AnalyticsSuite\Contracts\AnalyticsEngineInterface;
use Mostafax\AnalyticsSuite\Contracts\DetectionEngineInterface;
use Mostafax\AnalyticsSuite\DTOs\AnalyticsResultDTO;
use Mostafax\AnalyticsSuite\DTOs\WidgetDataDTO;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\WidgetModel;
use Mostafax\ReportingEngine\Core\Engine\ReportEngine;
use Mostafax\ReportingEngine\Domain\Execution\ExecutionResult;

final class AnalyticsEngine implements AnalyticsEngineInterface
{
    public function __construct(
        private readonly ReportEngine            $reportEngine,
        private readonly AnalyticsCacheManager   $cache,
        private readonly DetectionEngineInterface $detection,
    ) {}

    public function executeWidget(int|string $widgetId, array $params = []): WidgetDataDTO
    {
        $cacheKey = "widget_{$widgetId}_" . md5(serialize($params));

        $cached = $this->cache->getWidget($cacheKey);
        if ($cached !== null) {
            return new WidgetDataDTO(
                widgetId:    $widgetId,
                type:        $cached['type'],
                data:        $cached['data'],
                meta:        $cached['meta'],
                fromCache:   true,
                cachedAt:    $cached['cached_at'],
                executionMs: 0,
            );
        }

        $widget = WidgetModel::findOrFail($widgetId);
        $start  = microtime(true);

        $dsl = $this->buildDslFromWidget($widget, $params);
        /** @var ExecutionResult $result */
        $result = $this->reportEngine->run($dsl);

        $ms = (microtime(true) - $start) * 1000;

        $widgetData = new WidgetDataDTO(
            widgetId:    $widgetId,
            type:        $widget->type,
            data:        $result->rows,
            meta:        [
                'total'       => $result->total,
                'columns'     => $result->columns,
                'aggregations'=> $result->aggregations ?? [],
            ],
            fromCache:   false,
            cachedAt:    null,
            executionMs: round($ms, 2),
        );

        if ($widget->cache_enabled) {
            $this->cache->putWidget($cacheKey, $widgetData->toArray(), $widget->cache_ttl);
        }

        return $widgetData;
    }

    public function executeDashboard(int|string $dashboardId, array $params = []): Collection
    {
        $widgets = WidgetModel::forDashboard($dashboardId)->get();
        return $widgets->map(fn ($widget) =>
            $this->executeWidget($widget->id, $params)
        );
    }

    public function computeStats(string $model, array $config = []): AnalyticsResultDTO
    {
        $cacheKey = "stats_{$model}_" . md5(serialize($config));
        $cached   = $this->cache->getStats($cacheKey);

        if ($cached !== null) {
            return new AnalyticsResultDTO(
                module:      $model,
                stats:       $cached['stats'],
                trends:      $cached['trends'],
                comparisons: $cached['comparisons'],
                fromCache:   true,
                executionMs: 0,
                generatedAt: $cached['generated_at'],
            );
        }

        $start = microtime(true);

        $instance = new $model();
        $table    = $instance->getTable();

        $total = \DB::table($table)->count();
        $today = \DB::table($table)
            ->whereDate('created_at', today())
            ->count();

        $stats = [
            'total'   => $total,
            'today'   => $today,
            'table'   => $table,
        ];

        $trends = $this->computeTrends($table, $config);

        $ms      = (microtime(true) - $start) * 1000;
        $dto     = new AnalyticsResultDTO(
            module:      $model,
            stats:       $stats,
            trends:      $trends,
            comparisons: [],
            fromCache:   false,
            executionMs: round($ms, 2),
            generatedAt: now()->toISOString(),
        );

        $ttl = config('analytics-suite.cache.ttl.stats', 120);
        $this->cache->putStats($cacheKey, $dto->toArray(), $ttl);

        return $dto;
    }

    public function getAvailableModules(): Collection
    {
        return $this->detection->detectModels()
            ->map(fn ($dto) => [
                'class'  => $dto->class,
                'name'   => $dto->name,
                'table'  => $dto->table,
                'module' => $dto->module,
            ]);
    }

    public function generateSummary(string $module): AnalyticsResultDTO
    {
        return $this->computeStats($module);
    }

    public function refreshCache(int|string $widgetId): bool
    {
        $this->cache->invalidateWidget($widgetId);
        return true;
    }

    public function invalidateDashboardCache(int|string $dashboardId): bool
    {
        $this->cache->invalidateDashboard($dashboardId);
        return true;
    }

    // -------------------------------------------------------------------------

    private function buildDslFromWidget(WidgetModel $widget, array $params): array
    {
        $config = array_merge($widget->config ?? [], $params);

        return [
            'source'       => $config['data_source'] ?? $config['table'] ?? null,
            'source_type'  => $config['source_type'] ?? 'mysql',
            'select'       => $config['columns'] ?? [],
            'filters'      => $config['filters'] ?? [],
            'group_by'     => isset($config['group_by']) ? [$config['group_by']] : [],
            'order_by'     => $config['order_by'] ?? [],
            'aggregations' => $this->buildAggregations($widget->type, $config),
            'limit'        => $config['limit'] ?? 100,
        ];
    }

    private function buildAggregations(string $widgetType, array $config): array
    {
        return match (true) {
            in_array($widgetType, ['kpi_card', 'stats_card'], true) => [[
                'function' => $config['aggregation'] ?? 'count',
                'column'   => $config['column'] ?? '*',
                'alias'    => 'value',
            ]],
            in_array($widgetType, ['line_chart', 'bar_chart', 'area_chart'], true) => [[
                'function' => $config['aggregation'] ?? 'count',
                'column'   => $config['column'] ?? '*',
                'alias'    => 'y',
            ]],
            default => [],
        };
    }

    private function computeTrends(string $table, array $config): array
    {
        try {
            $results = \DB::table($table)
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, COUNT(*) as count')
                ->whereNotNull('created_at')
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->limit(12)
                ->get()
                ->toArray();

            return array_reverse($results);
        } catch (\Throwable) {
            return [];
        }
    }
}
