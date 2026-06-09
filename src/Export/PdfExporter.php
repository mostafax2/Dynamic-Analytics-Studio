<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Export;

final class PdfExporter implements ExporterContract
{
    public function generate(array $rows, array $options = []): string
    {
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class) && !class_exists(\Dompdf\Dompdf::class)) {
            throw new \RuntimeException('barryvdh/laravel-dompdf is required for PDF export. Run: composer require barryvdh/laravel-dompdf');
        }

        $title  = $options['title']  ?? 'Analytics Report';
        $rtl    = $options['rtl']    ?? config('analytics-suite.export.rtl', false);
        $locale = $options['locale'] ?? config('analytics-suite.export.locale', 'en');

        $html = $this->buildHtml($rows, $title, $rtl, $locale);

        $dompdf = new \Dompdf\Dompdf(['enable_html5_parser' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }

    public function mimeType(): string
    {
        return 'application/pdf';
    }

    public function extension(): string
    {
        return 'pdf';
    }

    private function buildHtml(array $rows, string $title, bool $rtl, string $locale): string
    {
        $dir     = $rtl ? 'rtl' : 'ltr';
        $headers = !empty($rows) ? array_keys((array) $rows[0]) : [];
        $date    = now()->format('Y-m-d H:i');

        $headerCells = implode('', array_map(
            fn ($h) => "<th style='background:#6366f1;color:#fff;padding:8px;border:1px solid #ddd;'>{$h}</th>",
            $headers
        ));

        $bodyRows = implode('', array_map(function ($row, $i) {
            $bg   = $i % 2 === 0 ? '#fff' : '#f8f9fa';
            $cells = implode('', array_map(
                fn ($v) => "<td style='padding:6px 8px;border:1px solid #ddd;'>" . htmlspecialchars((string) $v) . "</td>",
                array_values((array) $row)
            ));
            return "<tr style='background:{$bg};'>{$cells}</tr>";
        }, $rows, array_keys($rows)));

        return <<<HTML
<!DOCTYPE html>
<html dir="{$dir}" lang="{$locale}">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; direction: {$dir}; }
  table { width: 100%; border-collapse: collapse; }
  h1 { color: #1e293b; font-size: 18px; margin-bottom: 4px; }
  .meta { color: #64748b; font-size: 10px; margin-bottom: 16px; }
</style>
</head>
<body>
  <h1>{$title}</h1>
  <div class="meta">Generated: {$date} · Total records: {count($rows)}</div>
  <table>
    <thead><tr>{$headerCells}</tr></thead>
    <tbody>{$bodyRows}</tbody>
  </table>
</body>
</html>
HTML;
    }
}
