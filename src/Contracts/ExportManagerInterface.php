<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Contracts;

use Mostafax\AnalyticsSuite\DTOs\ExportResultDTO;

interface ExportManagerInterface
{
    public function export(string $format, array $data, array $options = []): ExportResultDTO;

    public function exportReport(int|string $reportId, string $format, array $params = []): ExportResultDTO;

    public function exportWidget(int|string $widgetId, string $format, array $params = []): ExportResultDTO;

    public function exportDashboard(int|string $dashboardId, string $format, array $params = []): ExportResultDTO;

    public function queueExport(string $type, int|string $id, string $format, array $params = [], ?string $notifyEmail = null): string;

    public function supportedFormats(): array;
}
