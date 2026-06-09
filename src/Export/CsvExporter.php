<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Export;

final class CsvExporter implements ExporterContract
{
    public function generate(array $rows, array $options = []): string
    {
        if (empty($rows)) {
            return '';
        }

        $delimiter = $options['delimiter'] ?? ',';
        $handle    = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, array_keys((array) $rows[0]), $delimiter);

        foreach ($rows as $row) {
            fputcsv($handle, array_values((array) $row), $delimiter);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        // BOM for RTL/Arabic Excel support
        if ($options['bom'] ?? false) {
            $csv = "\xEF\xBB\xBF" . $csv;
        }

        return $csv;
    }

    public function mimeType(): string
    {
        return 'text/csv';
    }

    public function extension(): string
    {
        return 'csv';
    }
}
