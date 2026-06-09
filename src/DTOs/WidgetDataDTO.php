<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\DTOs;

final class WidgetDataDTO
{
    public function __construct(
        public readonly int|string $widgetId,
        public readonly string     $type,
        public readonly mixed      $data,
        public readonly array      $meta,
        public readonly bool       $fromCache,
        public readonly ?string    $cachedAt,
        public readonly float      $executionMs,
        public readonly ?string    $error = null,
    ) {}

    public function toArray(): array
    {
        return [
            'widget_id'    => $this->widgetId,
            'type'         => $this->type,
            'data'         => $this->data,
            'meta'         => $this->meta,
            'from_cache'   => $this->fromCache,
            'cached_at'    => $this->cachedAt,
            'execution_ms' => $this->executionMs,
            'error'        => $this->error,
        ];
    }
}
