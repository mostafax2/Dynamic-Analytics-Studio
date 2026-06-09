<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WidgetModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'as_widgets';

    protected $fillable = [
        'dashboard_id', 'type', 'title', 'description',
        'config', 'position', 'styling',
        'refresh_interval', 'cache_enabled', 'cache_ttl',
        'report_id', 'report_params', 'created_by', 'tenant_id',
    ];

    protected $casts = [
        'config'         => 'array',
        'position'       => 'array',
        'styling'        => 'array',
        'report_params'  => 'array',
        'cache_enabled'  => 'boolean',
        'refresh_interval'=> 'integer',
        'cache_ttl'      => 'integer',
    ];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(DashboardModel::class, 'dashboard_id');
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(WidgetSnapshotModel::class, 'widget_id');
    }

    public function latestSnapshot(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WidgetSnapshotModel::class, 'widget_id')
            ->latestOfMany('captured_at');
    }

    public function scopeForDashboard($query, int|string $dashboardId)
    {
        return $query->where('dashboard_id', $dashboardId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
