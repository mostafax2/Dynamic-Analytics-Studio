<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WidgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = is_array($this->resource) ? $this->resource : (
            method_exists($this->resource, 'toArray')
                ? $this->resource->toArray()
                : (array) $this->resource
        );

        return [
            'id'               => $data['id'],
            'dashboard_id'     => $data['dashboard_id'],
            'type'             => $data['type'],
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'config'           => $data['config'] ?? [],
            'position'         => $data['position'] ?? [],
            'styling'          => $data['styling'] ?? [],
            'refresh_interval' => $data['refresh_interval'] ?? 300,
            'cache_enabled'    => $data['cache_enabled'] ?? true,
            'cache_ttl'        => $data['cache_ttl'] ?? 300,
            'report_id'        => $data['report_id'] ?? null,
            'report_params'    => $data['report_params'] ?? [],
            'created_at'       => $data['created_at'] ?? null,
            'updated_at'       => $data['updated_at'] ?? null,
        ];
    }
}
