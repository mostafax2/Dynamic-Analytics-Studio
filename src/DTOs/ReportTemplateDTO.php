<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\DTOs;

final class ReportTemplateDTO
{
    public function __construct(
        public readonly int|string  $id,
        public readonly string      $name,
        public readonly ?string     $description,
        public readonly string      $dataSource,
        public readonly string      $sourceType,     // mysql | mongodb
        public readonly array       $columns,
        public readonly array       $filters,
        public readonly array       $groupBy,
        public readonly array       $orderBy,
        public readonly array       $aggregations,
        public readonly array       $joins,
        public readonly array       $settings,
        public readonly bool        $isTemplate,
        public readonly int|string|null $createdBy,
        public readonly int|string|null $tenantId,
        public readonly string      $createdAt,
        public readonly string      $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id:           $data['id'],
            name:         $data['name'],
            description:  $data['description'] ?? null,
            dataSource:   $data['data_source'],
            sourceType:   $data['source_type'] ?? 'mysql',
            columns:      $data['columns'] ?? [],
            filters:      $data['filters'] ?? [],
            groupBy:      $data['group_by'] ?? [],
            orderBy:      $data['order_by'] ?? [],
            aggregations: $data['aggregations'] ?? [],
            joins:        $data['joins'] ?? [],
            settings:     $data['settings'] ?? [],
            isTemplate:   (bool) ($data['is_template'] ?? false),
            createdBy:    $data['created_by'],
            tenantId:     $data['tenant_id'] ?? null,
            createdAt:    $data['created_at'] ?? now()->toISOString(),
            updatedAt:    $data['updated_at'] ?? now()->toISOString(),
        );
    }

    public function toDslArray(): array
    {
        return [
            'source'       => $this->dataSource,
            'source_type'  => $this->sourceType,
            'select'       => $this->columns,
            'filters'      => $this->filters,
            'group_by'     => $this->groupBy,
            'order_by'     => $this->orderBy,
            'aggregations' => $this->aggregations,
            'joins'        => $this->joins,
        ];
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'data_source'  => $this->dataSource,
            'source_type'  => $this->sourceType,
            'columns'      => $this->columns,
            'filters'      => $this->filters,
            'group_by'     => $this->groupBy,
            'order_by'     => $this->orderBy,
            'aggregations' => $this->aggregations,
            'joins'        => $this->joins,
            'settings'     => $this->settings,
            'is_template'  => $this->isTemplate,
            'created_by'   => $this->createdBy,
            'tenant_id'    => $this->tenantId,
            'created_at'   => $this->createdAt,
            'updated_at'   => $this->updatedAt,
        ];
    }
}
