<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Scheduling;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\Contracts\SchedulerInterface;
use Mostafax\AnalyticsSuite\DTOs\ScheduledReportDTO;
use Mostafax\AnalyticsSuite\Export\ExportManager;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\ReportTemplateModel;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\ScheduledReportModel;

final class ReportScheduler implements SchedulerInterface
{
    private const FREQUENCY_MAP = [
        'daily'     => '0 8 * * *',
        'weekly'    => '0 8 * * 1',
        'monthly'   => '0 8 1 * *',
        'quarterly' => '0 8 1 1,4,7,10 *',
        'yearly'    => '0 8 1 1 *',
    ];

    public function __construct(private readonly ExportManager $exportManager) {}

    public function schedule(array $data): ScheduledReportDTO
    {
        $data['cron_expression'] ??= self::FREQUENCY_MAP[$data['frequency']] ?? '0 8 * * *';
        $data['next_run_at']      = $this->nextRun($data['cron_expression']);
        $data['created_by']       = auth()->id();

        $model = ScheduledReportModel::create($data);
        return ScheduledReportDTO::fromArray($model->toArray());
    }

    public function update(int|string $id, array $data): ScheduledReportDTO
    {
        $model = ScheduledReportModel::findOrFail($id);

        if (isset($data['frequency']) && !isset($data['cron_expression'])) {
            $data['cron_expression'] = self::FREQUENCY_MAP[$data['frequency']] ?? $model->cron_expression;
        }
        if (isset($data['cron_expression'])) {
            $data['next_run_at'] = $this->nextRun($data['cron_expression']);
        }

        $model->update($data);
        return ScheduledReportDTO::fromArray($model->fresh()->toArray());
    }

    public function cancel(int|string $id): bool
    {
        return (bool) ScheduledReportModel::findOrFail($id)->delete();
    }

    public function pause(int|string $id): bool
    {
        return (bool) ScheduledReportModel::where('id', $id)->update(['is_active' => false]);
    }

    public function resume(int|string $id): bool
    {
        $model = ScheduledReportModel::findOrFail($id);
        $model->update([
            'is_active'    => true,
            'next_run_at'  => $this->nextRun($model->cron_expression),
            'failure_count'=> 0,
        ]);
        return true;
    }

    public function listForUser(int|string $userId): Collection
    {
        return ScheduledReportModel::where('created_by', $userId)
            ->latest()
            ->get()
            ->map(fn ($m) => ScheduledReportDTO::fromArray($m->toArray()));
    }

    public function getDueReports(): Collection
    {
        return ScheduledReportModel::due()
            ->get()
            ->map(fn ($m) => ScheduledReportDTO::fromArray($m->toArray()));
    }

    public function dispatch(int|string $scheduledReportId): bool
    {
        $scheduled = ScheduledReportModel::with('report')->findOrFail($scheduledReportId);

        if (!$scheduled->is_active) {
            return false;
        }

        $report = $scheduled->report;
        if (!$report) {
            return false;
        }

        // Generate export
        $result = $this->exportManager->exportReport(
            $report->id,
            $scheduled->format,
            $scheduled->params ?? []
        );

        // Deliver
        if (in_array('email', $scheduled->delivery_methods, true)) {
            $this->deliverEmail($scheduled, $result);
        }
        if (in_array('webhook', $scheduled->delivery_methods, true) && $scheduled->webhook_url) {
            $this->deliverWebhook($scheduled, $result);
        }

        // Update next_run_at
        $scheduled->update([
            'last_run_at' => now(),
            'next_run_at' => $this->nextRun($scheduled->cron_expression),
        ]);

        return true;
    }

    // -------------------------------------------------------------------------

    private function nextRun(string $expression): string
    {
        try {
            if (class_exists(\Cron\CronExpression::class)) {
                $cron = new \Cron\CronExpression($expression);
                return $cron->getNextRunDate()->format('Y-m-d H:i:s');
            }
        } catch (\Throwable) {}

        return now()->addDay()->format('Y-m-d H:i:s');
    }

    private function deliverEmail(ScheduledReportModel $scheduled, object $result): void
    {
        foreach ($scheduled->recipients as $recipient) {
            \Mail::raw(
                "Scheduled report '{$scheduled->name}' is ready.\nFile: {$result->filename}\nDownload: {$result->downloadUrl}",
                fn ($m) => $m
                    ->to($recipient)
                    ->subject("Report: {$scheduled->name}")
                    ->from(
                        config('analytics-suite.scheduling.from_email'),
                        config('analytics-suite.scheduling.from_name')
                    )
            );
        }
    }

    private function deliverWebhook(ScheduledReportModel $scheduled, object $result): void
    {
        \Http::post($scheduled->webhook_url, [
            'scheduled_report_id' => $scheduled->id,
            'report_name'         => $scheduled->name,
            'format'              => $scheduled->format,
            'download_url'        => $result->downloadUrl,
            'rows'                => $result->rows,
            'generated_at'        => $result->generatedAt,
        ]);
    }
}
