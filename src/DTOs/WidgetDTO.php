<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\DTOs;

final class WidgetDTO
{
    public function __construct(
        public readonly int|string  $id,
        public readonly int|string  $dashboardId,
        public readonly string      $type,
        public readonly string      $title,
        public readonly ?string     $description,
        public readonly array       $config,
        public readonly array       $position,
        public readonly array       $styling,
        public readonly int         $refreshInterval,
        public readonly bool        $cacheEnabled,
        public readonly int         $cacheTtl,
        public readonly ?int|string $reportId,
        public readonly array       $reportParams,
        public readonly int|string  $createdBy,
        public readonly ?int|string $tenantId,
        public readonly string      $createdAt,
        public readonly string      $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id:              $data['id'],
            dashboardId:     $data['dashboard_id'],
            type:            $data['type'],
            title:           $data['title'],
            description:     $data['description'] ?? null,
            config:          $data['config'] ?? [],
            position:        $data['position'] ?? ['x' => 0, 'y' => 0, 'w' => 4, 'h' => 3],
            styling:         $data['styling'] ?? [],
            refreshInterval: (int) ($data['refresh_interval'] ?? 300),
            cacheEnabled:    (bool) ($data['cache_enabled'] ?? true),
            cacheTtl:        (int) ($data['cache_ttl'] ?? 300),
            reportId:        $data['report_id'] ?? null,
            reportParams:    $data['report_params'] ?? [],
            createdBy:       $data['created_by'],
            tenantId:        $data['tenant_id'] ?? null,
            createdAt:       $data['created_at'] ?? now()->toISOString(),
            updatedAt:       $data['updated_at'] ?? now()->toISOString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'dashboard_id'     => $this->dashboardId,
            'type'             => $this->type,
            'title'            => $this->title,
            'description'      => $this->description,
            'config'           => $this->config,
            'position'         => $this->position,
            'styling'          => $this->styling,
            'refresh_interval' => $this->refreshInterval,
            'cache_enabled'    => $this->cacheEnabled,
            'cache_ttl'        => $this->cacheTtl,
            'report_id'        => $this->reportId,
            'report_params'    => $this->reportParams,
            'created_by'       => $this->createdBy,
            'tenant_id'        => $this->tenantId,
            'created_at'       => $this->createdAt,
            'updated_at'       => $this->updatedAt,
        ];
    }
}
