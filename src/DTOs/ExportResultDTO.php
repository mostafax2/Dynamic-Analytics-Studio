<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\DTOs;

final class ExportResultDTO
{
    public function __construct(
        public readonly string  $format,
        public readonly string  $disk,
        public readonly string  $path,
        public readonly string  $filename,
        public readonly int     $rows,
        public readonly int     $sizeBytes,
        public readonly string  $mimeType,
        public readonly float   $generationMs,
        public readonly string  $generatedAt,
        public readonly ?string $downloadUrl = null,
    ) {}

    public function toArray(): array
    {
        return [
            'format'        => $this->format,
            'disk'          => $this->disk,
            'path'          => $this->path,
            'filename'      => $this->filename,
            'rows'          => $this->rows,
            'size_bytes'    => $this->sizeBytes,
            'mime_type'     => $this->mimeType,
            'generation_ms' => $this->generationMs,
            'generated_at'  => $this->generatedAt,
            'download_url'  => $this->downloadUrl,
        ];
    }
}
