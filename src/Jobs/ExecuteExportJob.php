<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mostafax\AnalyticsSuite\Export\ExportManager;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\ExportJobModel;

final class ExecuteExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 300;

    public function __construct(
        private readonly int $exportJobId,
    ) {
        $this->queue = config('analytics-suite.export.queue', 'default');
    }

    public function handle(ExportManager $exportManager): void
    {
        $job = ExportJobModel::findOrFail($this->exportJobId);
        $job->markProcessing();

        try {
            $result = $exportManager->export(
                $job->format,
                ['export_job_id' => $job->id],
                array_merge($job->params ?? [], [
                    'type'        => $job->type,
                    'resource_id' => $job->resource_id,
                ])
            );

            $job->markDone(
                $result->disk,
                $result->path,
                $result->filename,
                $result->rows,
                $result->sizeBytes,
            );

            if ($job->notify_email) {
                \Mail::raw(
                    "Your export is ready: {$result->filename}",
                    fn ($m) => $m->to($job->notify_email)->subject('Export Ready')
                );
            }
        } catch (\Throwable $e) {
            $job->markFailed($e->getMessage());
            throw $e;
        }
    }
}
