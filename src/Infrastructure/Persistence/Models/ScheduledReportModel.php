<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledReportModel extends Model
{
    use HasFactory;

    protected $table = 'as_scheduled_reports';

    protected $fillable = [
        'report_id', 'name', 'frequency', 'cron_expression',
        'format', 'delivery_methods', 'recipients', 'webhook_url',
        'params', 'is_active', 'last_run_at', 'next_run_at',
        'failure_count', 'last_error', 'created_by', 'tenant_id',
    ];

    protected $casts = [
        'delivery_methods' => 'array',
        'recipients'       => 'array',
        'params'           => 'array',
        'is_active'        => 'boolean',
        'last_run_at'      => 'datetime',
        'next_run_at'      => 'datetime',
        'failure_count'    => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(ReportTemplateModel::class, 'report_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'created_by');
    }

    public function scopeDue($query)
    {
        return $query->where('is_active', true)
            ->where('next_run_at', '<=', now());
    }

    public function markRan(bool $success = true, ?string $error = null): void
    {
        $this->last_run_at = now();
        if ($success) {
            $this->failure_count = 0;
            $this->last_error    = null;
        } else {
            $this->failure_count++;
            $this->last_error = $error;
            if ($this->failure_count >= 5) {
                $this->is_active = false;
            }
        }
        $this->save();
    }
}
