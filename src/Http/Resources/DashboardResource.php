<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Support both DTO and Eloquent model
        $data = is_array($this->resource) ? $this->resource : (
            method_exists($this->resource, 'toArray')
                ? $this->resource->toArray()
                : (array) $this->resource
        );

        return [
            'id'               => $data['id'],
            'name'             => $data['name'],
            'slug'             => $data['slug'],
            'description'      => $data['description'] ?? null,
            'layout'           => $data['layout'] ?? [],
            'settings'         => $data['settings'] ?? [],
            'is_public'        => $data['is_public'] ?? false,
            'public_token'     => $data['public_token'] ?? null,
            'public_expires_at'=> $data['public_expires_at'] ?? null,
            'is_default'       => $data['is_default'] ?? false,
            'widget_count'     => count($data['widgets'] ?? []),
            'widgets'          => WidgetResource::collection(collect($data['widgets'] ?? [])),
            'created_at'       => $data['created_at'] ?? null,
            'updated_at'       => $data['updated_at'] ?? null,
        ];
    }
}
