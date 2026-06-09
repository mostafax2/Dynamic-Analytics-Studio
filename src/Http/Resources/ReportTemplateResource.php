<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'data_source'  => $this->data_source,
            'source_type'  => $this->source_type,
            'columns'      => $this->columns ?? [],
            'filters'      => $this->filters ?? [],
            'group_by'     => $this->group_by ?? [],
            'order_by'     => $this->order_by ?? [],
            'aggregations' => $this->aggregations ?? [],
            'joins'        => $this->joins ?? [],
            'settings'     => $this->settings ?? [],
            'is_template'  => $this->is_template,
            'category'     => $this->category,
            'tags'         => $this->tags ?? [],
            'created_by'   => $this->created_by,
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
