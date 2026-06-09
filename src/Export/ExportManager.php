<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Export;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mostafax\AnalyticsSuite\Contracts\ExportManagerInterface;
use Mostafax\AnalyticsSuite\DTOs\ExportResultDTO;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\ExportJobModel;
use Mostafax\AnalyticsSuite\Jobs\ExecuteExportJob;

final class ExportManager implements ExportManagerInterface
{
    private array $exporters = [];

    public function __construct()
    {
        $this->bootExporters();
    }

    public function export(string $format, array $data, array $options = []): ExportResultDTO
    {
        $exporter = $this->resolveExporter($format);
        $start    = microtime(true);

        $disk     = config('analytics-suite.export.disk', 'local');
        $basePath = config('analytics-suite.export.path', 'analytics/exports');
        $filename = $this->buildFilename($format, $options['label'] ?? 'export');

        $path = $basePath . '/' . $filename;
        $rows = count($data['rows'] ?? $data);

        $content = $exporter->generate($data['rows'] ?? $data, $options);

        Storage::disk($disk)->put($path, $content);
        $sizeBytes = Storage::disk($disk)->size($path);

        $ms = (microtime(true) - $start) * 1000;

        return new ExportResultDTO(
            format:       $format,
            disk:         $disk,
            path:         $path,
            filename:     $filename,
            rows:         $rows,
            sizeBytes:    $sizeBytes,
            mimeType:     $exporter->mimeType(),
            generationMs: round($ms, 2),
            generatedAt:  now()->toISOString(),
            downloadUrl:  Storage::disk($disk)->temporaryUrl($path, now()->addHour()),
        );
    }

    public function exportReport(int|string $reportId, string $format, array $params = []): ExportResultDTO
    {
        return $this->export($format, $params, array_merge($params, [
            'type'        => 'report',
            'resource_id' => $reportId,
        ]));
    }

    public function exportWidget(int|string $widgetId, string $format, array $params = []): ExportResultDTO
    {
        return $this->export($format, $params, array_merge($params, [
            'type'        => 'widget',
            'resource_id' => $widgetId,
        ]));
    }

    public function exportDashboard(int|string $dashboardId, string $format, array $params = []): ExportResultDTO
    {
        return $this->export($format, $params, array_merge($params, [
            'type'        => 'dashboard',
            'resource_id' => $dashboardId,
        ]));
    }

    public function queueExport(string $type, int|string $id, string $format, array $params = [], ?string $notifyEmail = null): string
    {
        $job = ExportJobModel::create([
            'type'         => $type,
            'resource_id'  => $id,
            'format'       => $format,
            'params'       => $params,
            'notify_email' => $notifyEmail,
            'created_by'   => auth()->id(),
            'status'       => 'pending',
        ]);

        ExecuteExportJob::dispatch($job->id);

        return (string) $job->id;
    }

    public function supportedFormats(): array
    {
        return array_keys($this->exporters);
    }

    // -------------------------------------------------------------------------

    private function bootExporters(): void
    {
        $this->exporters = [
            'csv'   => new CsvExporter(),
            'json'  => new JsonExporter(),
            'excel' => new ExcelExporter(),
            'pdf'   => new PdfExporter(),
        ];
    }

    private function resolveExporter(string $format): ExporterContract
    {
        if (!isset($this->exporters[$format])) {
            throw new \InvalidArgumentException("Unsupported export format: {$format}");
        }
        return $this->exporters[$format];
    }

    private function buildFilename(string $format, string $label): string
    {
        $slug = Str::slug($label);
        $ts   = now()->format('Y-m-d_His');
        return "{$slug}_{$ts}.{$format}";
    }
}
