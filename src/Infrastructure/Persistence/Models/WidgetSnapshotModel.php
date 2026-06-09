<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WidgetSnapshotModel extends Model
{
    public $timestamps = false;

    protected $table = 'as_widget_snapshots';

    protected $fillable = [
        'widget_id', 'data', 'meta',
        'execution_ms', 'rows', 'captured_at', 'tenant_id',
    ];

    protected $casts = [
        'data'        => 'array',
        'meta'        => 'array',
        'captured_at' => 'datetime',
    ];

    public function widget(): BelongsTo
    {
        return $this->belongsTo(WidgetModel::class, 'widget_id');
    }
}
