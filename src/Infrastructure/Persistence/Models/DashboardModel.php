<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DashboardModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'as_dashboards';

    protected $fillable = [
        'name', 'slug', 'description', 'created_by',
        'layout', 'settings', 'is_public', 'public_token',
        'public_expires_at', 'is_default', 'permissions', 'tenant_id',
    ];

    protected $casts = [
        'layout'           => 'array',
        'settings'         => 'array',
        'permissions'      => 'array',
        'is_public'        => 'boolean',
        'is_default'       => 'boolean',
        'public_expires_at'=> 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name) . '-' . Str::random(6);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'created_by');
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(WidgetModel::class, 'dashboard_id');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(DashboardShareModel::class, 'dashboard_id');
    }

    public function scopeForTenant($query, int|string|null $tenantId)
    {
        if ($tenantId !== null) {
            return $query->where('tenant_id', $tenantId);
        }
        return $query;
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true)
            ->where(fn ($q) => $q->whereNull('public_expires_at')
                ->orWhere('public_expires_at', '>', now()));
    }

    public function generatePublicToken(): string
    {
        return $this->public_token = Str::random(64);
    }
}
