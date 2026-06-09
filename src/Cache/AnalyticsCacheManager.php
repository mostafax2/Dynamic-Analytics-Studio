<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Cache;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\DTOs\DashboardDTO;

final class AnalyticsCacheManager
{
    private bool   $enabled;
    private string $prefix;

    public function __construct(private readonly CacheRepository $store)
    {
        $this->enabled = (bool) config('analytics-suite.cache.enabled', true);
        $this->prefix  = config('analytics-suite.cache.prefix', 'analytics_suite');
    }

    // ---- Dashboard ----------------------------------------------------------

    public function rememberDashboard(int|string $id, callable $callback): DashboardDTO
    {
        if (!$this->enabled) {
            return $callback();
        }

        $key = $this->key("dashboard:{$id}");
        $ttl = config('analytics-suite.cache.ttl.dashboard', 300);

        return $this->store->remember($key, $ttl, $callback);
    }

    public function rememberDashboardList(int|string $userId, callable $callback): Collection
    {
        if (!$this->enabled) {
            return $callback();
        }

        $key = $this->key("user:{$userId}:dashboards");
        $ttl = config('analytics-suite.cache.ttl.dashboard', 300);

        return $this->store->remember($key, $ttl, $callback);
    }

    public function invalidateDashboard(int|string $id): void
    {
        $this->store->forget($this->key("dashboard:{$id}"));
    }

    public function invalidateUserDashboardList(int|string $userId): void
    {
        $this->store->forget($this->key("user:{$userId}:dashboards"));
    }

    // ---- Widget -------------------------------------------------------------

    public function getWidget(string $cacheKey): ?array
    {
        if (!$this->enabled) {
            return null;
        }
        return $this->store->get($this->key("widget:{$cacheKey}"));
    }

    public function putWidget(string $cacheKey, array $data, int $ttl): void
    {
        if (!$this->enabled) {
            return;
        }
        $data['cached_at'] = now()->toISOString();
        $this->store->put($this->key("widget:{$cacheKey}"), $data, $ttl);
    }

    public function invalidateWidget(int|string $widgetId): void
    {
        // Pattern-based clear for all cached variants of the widget
        $this->store->forget($this->key("widget:widget_{$widgetId}"));
        // Full pattern flush — driver-dependent
        $this->flushPattern("widget:widget_{$widgetId}_");
    }

    // ---- Stats --------------------------------------------------------------

    public function getStats(string $cacheKey): ?array
    {
        if (!$this->enabled) {
            return null;
        }
        return $this->store->get($this->key("stats:{$cacheKey}"));
    }

    public function putStats(string $cacheKey, array $data, int $ttl): void
    {
        if (!$this->enabled) {
            return;
        }
        $this->store->put($this->key("stats:{$cacheKey}"), $data, $ttl);
    }

    // ---- Report -------------------------------------------------------------

    public function rememberReport(string $cacheKey, callable $callback, int $ttl = 0): mixed
    {
        if (!$this->enabled) {
            return $callback();
        }
        $ttl = $ttl ?: config('analytics-suite.cache.ttl.report', 600);
        return $this->store->remember($this->key("report:{$cacheKey}"), $ttl, $callback);
    }

    public function invalidateReport(string $cacheKey): void
    {
        $this->store->forget($this->key("report:{$cacheKey}"));
    }

    // ---- Schema -------------------------------------------------------------

    public function rememberSchema(string $table, callable $callback): mixed
    {
        $ttl = config('analytics-suite.cache.ttl.schema', 3600);
        return $this->store->remember($this->key("schema:{$table}"), $ttl, $callback);
    }

    // ---- Helpers ------------------------------------------------------------

    public function flush(): void
    {
        // Flush all analytics-suite keys (tag support required in driver)
        if (method_exists($this->store, 'tags')) {
            $this->store->tags([$this->prefix])->flush();
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    private function key(string $suffix): string
    {
        return "{$this->prefix}:{$suffix}";
    }

    private function flushPattern(string $pattern): void
    {
        // Redis-only: scan and delete matching keys
        try {
            $redis = $this->store->getStore();
            if (method_exists($redis, 'connection')) {
                $conn    = $redis->connection();
                $prefix  = $redis->getPrefix();
                $keys    = $conn->keys($prefix . $this->prefix . ':' . $pattern . '*');
                if (!empty($keys)) {
                    $conn->del($keys);
                }
            }
        } catch (\Throwable) {
            // Non-Redis drivers: single key delete already done above
        }
    }
}
