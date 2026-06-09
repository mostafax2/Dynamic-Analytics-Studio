<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\Contracts\WidgetRepositoryInterface;
use Mostafax\AnalyticsSuite\DTOs\WidgetDTO;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\WidgetModel;

final class EloquentWidgetRepository implements WidgetRepositoryInterface
{
    /** @var array<string, string> type => class */
    private array $typeRegistry = [];

    public function find(int|string $id): ?WidgetDTO
    {
        $model = WidgetModel::find($id);
        return $model ? WidgetDTO::fromArray($model->toArray()) : null;
    }

    public function listByDashboard(int|string $dashboardId): Collection
    {
        return WidgetModel::forDashboard($dashboardId)
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => WidgetDTO::fromArray($m->toArray()));
    }

    public function create(array $data): WidgetDTO
    {
        $model = WidgetModel::create($data);
        return WidgetDTO::fromArray($model->toArray());
    }

    public function update(int|string $id, array $data): WidgetDTO
    {
        $model = WidgetModel::findOrFail($id);
        $model->update($data);
        return WidgetDTO::fromArray($model->fresh()->toArray());
    }

    public function delete(int|string $id): bool
    {
        return (bool) WidgetModel::findOrFail($id)->delete();
    }

    public function clone(int|string $id): WidgetDTO
    {
        $original = WidgetModel::findOrFail($id);
        $clone    = $original->replicate(['created_at', 'updated_at', 'deleted_at']);
        $clone->title .= ' (Copy)';
        $clone->save();
        return WidgetDTO::fromArray($clone->toArray());
    }

    public function updatePosition(int|string $id, array $position): bool
    {
        return (bool) WidgetModel::where('id', $id)->update(['position' => $position]);
    }

    public function registerType(string $type, string $class): void
    {
        $this->typeRegistry[$type] = $class;
    }

    public function resolveType(string $type): string
    {
        return $this->typeRegistry[$type] ?? $type;
    }

    public function availableTypes(): Collection
    {
        $builtIn = collect(config('analytics-suite.widgets.types', []))
            ->map(fn ($t) => ['type' => $t, 'source' => 'built-in']);

        $custom = collect($this->typeRegistry)
            ->map(fn ($class, $type) => ['type' => $type, 'class' => $class, 'source' => 'marketplace']);

        return $builtIn->merge($custom);
    }
}
