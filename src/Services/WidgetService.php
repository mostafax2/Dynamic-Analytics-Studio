<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Services;

use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\Cache\AnalyticsCacheManager;
use Mostafax\AnalyticsSuite\Contracts\WidgetRepositoryInterface;
use Mostafax\AnalyticsSuite\DTOs\WidgetDTO;

final class WidgetService
{
    public function __construct(
        private readonly WidgetRepositoryInterface $repository,
        private readonly AnalyticsCacheManager     $cache,
    ) {}

    public function find(int|string $id): WidgetDTO
    {
        return $this->repository->find($id)
            ?? throw new \RuntimeException("Widget {$id} not found");
    }

    public function listByDashboard(int|string $dashboardId): Collection
    {
        return $this->repository->listByDashboard($dashboardId);
    }

    public function create(array $data, int|string|null $userId): WidgetDTO
    {
        $data['created_by'] = $userId;
        $widget = $this->repository->create($data);
        $this->cache->invalidateDashboard($widget->dashboardId);
        return $widget;
    }

    public function update(int|string $id, array $data): WidgetDTO
    {
        $widget = $this->repository->update($id, $data);
        $this->cache->invalidateWidget($id);
        $this->cache->invalidateDashboard($widget->dashboardId);
        return $widget;
    }

    public function delete(int|string $id): bool
    {
        $widget = $this->find($id);
        $result = $this->repository->delete($id);
        if ($result) {
            $this->cache->invalidateWidget($id);
            $this->cache->invalidateDashboard($widget->dashboardId);
        }
        return $result;
    }

    public function clone(int|string $id): WidgetDTO
    {
        return $this->repository->clone($id);
    }

    public function updatePosition(int|string $id, array $position): bool
    {
        $result = $this->repository->updatePosition($id, $position);
        if ($result) {
            $this->cache->invalidateWidget($id);
        }
        return $result;
    }

    public function registerCustomType(string $type, string $class): void
    {
        $this->repository->registerType($type, $class);
    }

    public function availableTypes(): Collection
    {
        return $this->repository->availableTypes();
    }
}
