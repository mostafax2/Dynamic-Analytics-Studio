<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\DTOs;

final class AnalyticsResultDTO
{
    public function __construct(
        public readonly string  $module,
        public readonly array   $stats,
        public readonly array   $trends,
        public readonly array   $comparisons,
        public readonly bool    $fromCache,
        public readonly float   $executionMs,
        public readonly string  $generatedAt,
    ) {}

    public function toArray(): array
    {
        return [
            'module'        => $this->module,
            'stats'         => $this->stats,
            'trends'        => $this->trends,
            'comparisons'   => $this->comparisons,
            'from_cache'    => $this->fromCache,
            'execution_ms'  => $this->executionMs,
            'generated_at'  => $this->generatedAt,
        ];
    }
}
