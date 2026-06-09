<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Export;

final class ExcelExporter implements ExporterContract
{
    public function generate(array $rows, array $options = []): string
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            throw new \RuntimeException('phpoffice/phpspreadsheet is required for Excel export. Run: composer require phpoffice/phpspreadsheet');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $title = $options['title'] ?? 'Analytics Report';
        $rtl   = $options['rtl']   ?? config('analytics-suite.export.rtl', false);

        $sheet->setTitle(substr($title, 0, 31));
        $sheet->setRightToLeft($rtl);

        if (!empty($rows)) {
            // Header row (bold)
            $headers = array_keys((array) $rows[0]);
            foreach ($headers as $colIndex => $header) {
                $cell = $sheet->getCellByColumnAndRow($colIndex + 1, 1);
                $cell->setValue($header);
                $cell->getStyle()->getFont()->setBold(true);
            }

            // Data rows
            foreach ($rows as $rowIndex => $row) {
                foreach (array_values((array) $row) as $colIndex => $value) {
                    $sheet->getCellByColumnAndRow($colIndex + 1, $rowIndex + 2)->setValue($value);
                }
            }

            // Auto-size columns
            foreach (range(1, count($headers)) as $col) {
                $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

    public function mimeType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    public function extension(): string
    {
        return 'xlsx';
    }
}
