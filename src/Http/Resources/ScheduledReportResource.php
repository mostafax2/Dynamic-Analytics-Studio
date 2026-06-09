<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduledReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = is_array($this->resource) ? $this->resource : $this->resource->toArray();

        return [
            'id'               => $data['id'],
            'report_id'        => $data['report_id'],
            'name'             => $data['name'],
            'frequency'        => $data['frequency'],
            'cron_expression'  => $data['cron_expression'],
            'format'           => $data['format'],
            'delivery_methods' => $data['delivery_methods'],
            'recipients'       => $data['recipients'],
            'webhook_url'      => $data['webhook_url'] ?? null,
            'is_active'        => $data['is_active'],
            'last_run_at'      => $data['last_run_at'] ?? null,
            'next_run_at'      => $data['next_run_at'] ?? null,
            'created_at'       => $data['created_at'],
            'updated_at'       => $data['updated_at'],
        ];
    }
}
