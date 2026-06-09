<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\DTOs;

use Illuminate\Support\Collection;

final class DetectedModelDTO
{
    public function __construct(
        public readonly string     $class,
        public readonly string     $name,
        public readonly string     $table,
        public readonly string     $module,
        public readonly array      $fillable,
        public readonly array      $casts,
        public readonly array      $relationships,
        public readonly array      $columns,
        public readonly bool       $hasSoftDeletes,
        public readonly string     $primaryKey,
        public readonly Collection $scopes,
    ) {}

    public function label(): string
    {
        return \Str::title(\Str::snake($this->name, ' '));
    }

    public function toArray(): array
    {
        return [
            'class'           => $this->class,
            'name'            => $this->name,
            'table'           => $this->table,
            'module'          => $this->module,
            'fillable'        => $this->fillable,
            'casts'           => $this->casts,
            'relationships'   => $this->relationships,
            'columns'         => $this->columns,
            'has_soft_deletes' => $this->hasSoftDeletes,
            'primary_key'     => $this->primaryKey,
            'scopes'          => $this->scopes->toArray(),
        ];
    }
}
