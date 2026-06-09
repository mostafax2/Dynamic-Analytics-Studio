<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\DTOs;

final class ScheduledReportDTO
{
    public function __construct(
        public readonly int|string  $id,
        public readonly int|string  $reportId,
        public readonly string      $name,
        public readonly string      $frequency,     // daily|weekly|monthly|quarterly|yearly
        public readonly string      $cronExpression,
        public readonly string      $format,
        public readonly array       $deliveryMethods,
        public readonly array       $recipients,
        public readonly ?string     $webhookUrl,
        public readonly array       $params,
        public readonly bool        $isActive,
        public readonly ?string     $lastRunAt,
        public readonly ?string     $nextRunAt,
        public readonly int|string  $createdBy,
        public readonly ?int|string $tenantId,
        public readonly string      $createdAt,
        public readonly string      $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id:              $data['id'],
            reportId:        $data['report_id'],
            name:            $data['name'],
            frequency:       $data['frequency'],
            cronExpression:  $data['cron_expression'],
            format:          $data['format'] ?? 'pdf',
            deliveryMethods: $data['delivery_methods'] ?? ['email'],
            recipients:      $data['recipients'] ?? [],
            webhookUrl:      $data['webhook_url'] ?? null,
            params:          $data['params'] ?? [],
            isActive:        (bool) ($data['is_active'] ?? true),
            lastRunAt:       $data['last_run_at'] ?? null,
            nextRunAt:       $data['next_run_at'] ?? null,
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
            'report_id'        => $this->reportId,
            'name'             => $this->name,
            'frequency'        => $this->frequency,
            'cron_expression'  => $this->cronExpression,
            'format'           => $this->format,
            'delivery_methods' => $this->deliveryMethods,
            'recipients'       => $this->recipients,
            'webhook_url'      => $this->webhookUrl,
            'params'           => $this->params,
            'is_active'        => $this->isActive,
            'last_run_at'      => $this->lastRunAt,
            'next_run_at'      => $this->nextRunAt,
            'created_by'       => $this->createdBy,
            'tenant_id'        => $this->tenantId,
            'created_at'       => $this->createdAt,
            'updated_at'       => $this->updatedAt,
        ];
    }
}
