<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Export;

interface ExporterContract
{
    public function generate(array $rows, array $options = []): string;

    public function mimeType(): string;

    public function extension(): string;
}
