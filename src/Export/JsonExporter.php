<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Export;

final class JsonExporter implements ExporterContract
{
    public function generate(array $rows, array $options = []): string
    {
        $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        return json_encode([
            'meta' => [
                'total'        => count($rows),
                'generated_at' => now()->toISOString(),
                'generated_by' => config('analytics-suite.name'),
            ],
            'data' => $rows,
        ], $flags);
    }

    public function mimeType(): string
    {
        return 'application/json';
    }

    public function extension(): string
    {
        return 'json';
    }
}
