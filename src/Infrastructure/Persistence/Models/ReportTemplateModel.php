<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportTemplateModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'as_report_templates';

    protected $fillable = [
        'name', 'description', 'data_source', 'source_type',
        'columns', 'filters', 'group_by', 'order_by',
        'aggregations', 'joins', 'settings', 'is_template',
        'category', 'tags', 'created_by', 'tenant_id',
    ];

    protected $casts = [
        'columns'      => 'array',
        'filters'      => 'array',
        'group_by'     => 'array',
        'order_by'     => 'array',
        'aggregations' => 'array',
        'joins'        => 'array',
        'settings'     => 'array',
        'tags'         => 'array',
        'is_template'  => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'created_by');
    }

    public function toDslDefinition(): array
    {
        return [
            'source'       => $this->data_source,
            'source_type'  => $this->source_type,
            'select'       => $this->columns,
            'filters'      => $this->filters,
            'group_by'     => $this->group_by,
            'order_by'     => $this->order_by,
            'aggregations' => $this->aggregations,
            'joins'        => $this->joins,
        ];
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForTenant($query, int|string|null $tenantId)
    {
        if ($tenantId !== null) {
            return $query->where('tenant_id', $tenantId);
        }
        return $query;
    }
}
