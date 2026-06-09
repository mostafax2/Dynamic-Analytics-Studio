<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mostafax\AnalyticsSuite\Events\ScheduledReportDispatched;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\ScheduledReportModel;
use Mostafax\AnalyticsSuite\Scheduling\ReportScheduler;

final class DispatchScheduledReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 600;

    public function __construct(private readonly int $scheduledReportId)
    {
        $this->queue = config('analytics-suite.scheduling.queue', 'default');
    }

    public function handle(ReportScheduler $scheduler): void
    {
        $record = ScheduledReportModel::findOrFail($this->scheduledReportId);

        try {
            $scheduler->dispatch($this->scheduledReportId);
            $record->markRan(true);

            event(new ScheduledReportDispatched(
                \Mostafax\AnalyticsSuite\DTOs\ScheduledReportDTO::fromArray($record->toArray())
            ));
        } catch (\Throwable $e) {
            $record->markRan(false, $e->getMessage());
            throw $e;
        }
    }
}
