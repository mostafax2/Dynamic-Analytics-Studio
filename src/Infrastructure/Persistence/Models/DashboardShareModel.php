<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DashboardShareModel extends Model
{
    protected $table = 'as_dashboard_shares';

    protected $fillable = [
        'dashboard_id', 'shareable_type', 'shareable_id',
        'permission', 'expires_at', 'granted_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(DashboardModel::class, 'dashboard_id');
    }

    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
