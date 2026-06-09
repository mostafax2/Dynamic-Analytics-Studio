<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\DTOs\DashboardDTO;

interface DashboardRepositoryInterface
{
    public function find(int|string $id): ?DashboardDTO;

    public function findBySlug(string $slug): ?DashboardDTO;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function listForUser(int|string $userId): Collection;

    public function create(array $data): DashboardDTO;

    public function update(int|string $id, array $data): DashboardDTO;

    public function delete(int|string $id): bool;

    public function clone(int|string $id, string $newName): DashboardDTO;

    public function addWidget(int|string $dashboardId, array $widgetData): array;

    public function updateWidgetLayout(int|string $dashboardId, array $layout): bool;

    public function findPublic(string $token): ?DashboardDTO;
}
