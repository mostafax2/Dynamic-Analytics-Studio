<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\Cache\AnalyticsCacheManager;
use Mostafax\AnalyticsSuite\Contracts\DashboardRepositoryInterface;
use Mostafax\AnalyticsSuite\DTOs\DashboardDTO;
use Mostafax\AnalyticsSuite\Events\DashboardCreated;
use Mostafax\AnalyticsSuite\Events\DashboardDeleted;
use Mostafax\AnalyticsSuite\Events\DashboardUpdated;

final class DashboardService
{
    public function __construct(
        private readonly DashboardRepositoryInterface $repository,
        private readonly AnalyticsCacheManager        $cache,
    ) {}

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function listForUser(int|string $userId): Collection
    {
        return $this->cache->rememberDashboardList($userId, fn () =>
            $this->repository->listForUser($userId)
        );
    }

    public function find(int|string $id): DashboardDTO
    {
        return $this->cache->rememberDashboard($id, fn () =>
            $this->repository->find($id) ?? throw new \RuntimeException("Dashboard {$id} not found")
        );
    }

    public function findPublic(string $token): DashboardDTO
    {
        return $this->repository->findPublic($token)
            ?? throw new \RuntimeException("Public dashboard not found or expired");
    }

    public function create(array $data, int|string $userId): DashboardDTO
    {
        $data['created_by'] = $userId;
        $dashboard = $this->repository->create($data);

        event(new DashboardCreated($dashboard));
        $this->cache->invalidateUserDashboardList($userId);

        return $dashboard;
    }

    public function update(int|string $id, array $data): DashboardDTO
    {
        $dashboard = $this->repository->update($id, $data);

        event(new DashboardUpdated($dashboard));
        $this->cache->invalidateDashboard($id);

        return $dashboard;
    }

    public function delete(int|string $id): bool
    {
        $result = $this->repository->delete($id);

        if ($result) {
            event(new DashboardDeleted($id));
            $this->cache->invalidateDashboard($id);
        }

        return $result;
    }

    public function clone(int|string $id, string $newName, int|string $userId): DashboardDTO
    {
        $dashboard = $this->repository->clone($id, $newName);
        $this->cache->invalidateUserDashboardList($userId);
        return $dashboard;
    }

    public function updateLayout(int|string $id, array $layout): bool
    {
        $result = $this->repository->updateWidgetLayout($id, $layout);
        if ($result) {
            $this->cache->invalidateDashboard($id);
        }
        return $result;
    }

    public function enablePublicShare(int|string $id, int $expiryDays = 7): DashboardDTO
    {
        $expiresAt = now()->addDays($expiryDays)->toDateTimeString();
        return $this->update($id, [
            'is_public'        => true,
            'public_token'     => \Str::random(64),
            'public_expires_at'=> $expiresAt,
        ]);
    }

    public function disablePublicShare(int|string $id): DashboardDTO
    {
        return $this->update($id, [
            'is_public'        => false,
            'public_token'     => null,
            'public_expires_at'=> null,
        ]);
    }

    public function export(int|string $id): array
    {
        $dashboard = $this->find($id);
        return $dashboard->toArray();
    }

    public function import(array $definition, int|string $userId): DashboardDTO
    {
        unset($definition['id'], $definition['created_at'], $definition['updated_at']);
        $definition['created_by'] = $userId;
        return $this->create($definition, $userId);
    }
}
