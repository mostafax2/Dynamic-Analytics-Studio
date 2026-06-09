<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mostafax\AnalyticsSuite\Contracts\DashboardRepositoryInterface;
use Mostafax\AnalyticsSuite\DTOs\DashboardDTO;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\DashboardModel;

final class EloquentDashboardRepository implements DashboardRepositoryInterface
{
    public function find(int|string $id): ?DashboardDTO
    {
        $model = DashboardModel::with('widgets')->find($id);
        return $model ? $this->toDTO($model) : null;
    }

    public function findBySlug(string $slug): ?DashboardDTO
    {
        $model = DashboardModel::with('widgets')->where('slug', $slug)->first();
        return $model ? $this->toDTO($model) : null;
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = DashboardModel::query()->with('widgets');

        if (isset($filters['tenant_id'])) {
            $query->forTenant($filters['tenant_id']);
        }
        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }
        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function listForUser(int|string $userId): Collection
    {
        return DashboardModel::with('widgets')
            ->where('created_by', $userId)
            ->latest()
            ->get()
            ->map(fn ($m) => $this->toDTO($m));
    }

    public function create(array $data): DashboardDTO
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        }
        $model = DashboardModel::create($data);
        return $this->toDTO($model->load('widgets'));
    }

    public function update(int|string $id, array $data): DashboardDTO
    {
        $model = DashboardModel::findOrFail($id);
        $model->update($data);
        return $this->toDTO($model->fresh('widgets'));
    }

    public function delete(int|string $id): bool
    {
        return (bool) DashboardModel::findOrFail($id)->delete();
    }

    public function clone(int|string $id, string $newName): DashboardDTO
    {
        $original = DashboardModel::with('widgets')->findOrFail($id);

        $clone = $original->replicate(['created_at', 'updated_at', 'deleted_at']);
        $clone->name         = $newName;
        $clone->slug         = Str::slug($newName) . '-' . Str::random(6);
        $clone->is_default   = false;
        $clone->public_token = null;
        $clone->is_public    = false;
        $clone->save();

        foreach ($original->widgets as $widget) {
            $widgetClone = $widget->replicate();
            $widgetClone->dashboard_id = $clone->id;
            $widgetClone->save();
        }

        return $this->toDTO($clone->load('widgets'));
    }

    public function addWidget(int|string $dashboardId, array $widgetData): array
    {
        $widgetData['dashboard_id'] = $dashboardId;
        $model = DashboardModel::findOrFail($dashboardId);
        $widget = $model->widgets()->create($widgetData);
        return $widget->toArray();
    }

    public function updateWidgetLayout(int|string $dashboardId, array $layout): bool
    {
        $dashboard = DashboardModel::with('widgets')->findOrFail($dashboardId);

        foreach ($layout as $item) {
            $dashboard->widgets()
                ->where('id', $item['widget_id'])
                ->update(['position' => [
                    'x' => $item['x'],
                    'y' => $item['y'],
                    'w' => $item['w'],
                    'h' => $item['h'],
                ]]);
        }

        return true;
    }

    public function findPublic(string $token): ?DashboardDTO
    {
        $model = DashboardModel::with('widgets')->public()
            ->where('public_token', $token)
            ->first();

        return $model ? $this->toDTO($model) : null;
    }

    private function toDTO(DashboardModel $model): DashboardDTO
    {
        return DashboardDTO::fromArray(array_merge(
            $model->toArray(),
            ['widgets' => $model->widgets ? $model->widgets->toArray() : []]
        ));
    }
}
