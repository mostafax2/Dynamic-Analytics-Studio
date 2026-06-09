<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Contracts;

use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\DTOs\WidgetDTO;

interface WidgetRepositoryInterface
{
    public function find(int|string $id): ?WidgetDTO;

    public function listByDashboard(int|string $dashboardId): Collection;

    public function create(array $data): WidgetDTO;

    public function update(int|string $id, array $data): WidgetDTO;

    public function delete(int|string $id): bool;

    public function clone(int|string $id): WidgetDTO;

    public function updatePosition(int|string $id, array $position): bool;

    public function registerType(string $type, string $class): void;

    public function resolveType(string $type): string;

    public function availableTypes(): Collection;
}
