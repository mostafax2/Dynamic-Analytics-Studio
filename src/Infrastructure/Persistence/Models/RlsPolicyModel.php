<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class RlsPolicyModel extends Model
{
    protected $table = 'as_rls_policies';

    protected $fillable = [
        'name', 'model', 'column', 'scope', 'operator',
        'value_source', 'value_key', 'conditions', 'is_active', 'tenant_id',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active'  => 'boolean',
    ];
}
