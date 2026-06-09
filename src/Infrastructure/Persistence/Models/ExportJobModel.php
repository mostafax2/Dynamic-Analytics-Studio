<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportJobModel extends Model
{
    use HasFactory;

    protected $table = 'as_export_jobs';

    protected $fillable = [
        'type', 'resource_id', 'format', 'params',
        'status', 'disk', 'path', 'filename',
        'rows', 'size_bytes', 'error',
        'notify_email', 'created_by', 'tenant_id', 'completed_at',
    ];

    protected $casts = [
        'params'       => 'array',
        'completed_at' => 'datetime',
    ];

    public function markProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markDone(string $disk, string $path, string $filename, int $rows, int $sizeBytes): void
    {
        $this->update([
            'status'       => 'done',
            'disk'         => $disk,
            'path'         => $path,
            'filename'     => $filename,
            'rows'         => $rows,
            'size_bytes'   => $sizeBytes,
            'completed_at' => now(),
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status'       => 'failed',
            'error'        => $error,
            'completed_at' => now(),
        ]);
    }
}
