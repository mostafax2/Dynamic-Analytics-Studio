<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\DTOs;

use Illuminate\Support\Collection;

final class DashboardDTO
{
    public function __construct(
        public readonly int|string    $id,
        public readonly string        $name,
        public readonly string        $slug,
        public readonly ?string       $description,
        public readonly int|string    $createdBy,
        public readonly array         $layout,
        public readonly array         $settings,
        public readonly bool          $isPublic,
        public readonly ?string       $publicToken,
        public readonly ?string       $publicExpiresAt,
        public readonly bool          $isDefault,
        public readonly Collection    $widgets,
        public readonly array         $permissions,
        public readonly ?int|string   $tenantId,
        public readonly string        $createdAt,
        public readonly string        $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id:              $data['id'],
            name:            $data['name'],
            slug:            $data['slug'] ?? \Str::slug($data['name']),
            description:     $data['description'] ?? null,
            createdBy:       $data['created_by'],
            layout:          $data['layout'] ?? [],
            settings:        $data['settings'] ?? [],
            isPublic:        (bool) ($data['is_public'] ?? false),
            publicToken:     $data['public_token'] ?? null,
            publicExpiresAt: $data['public_expires_at'] ?? null,
            isDefault:       (bool) ($data['is_default'] ?? false),
            widgets:         Collection::make($data['widgets'] ?? []),
            permissions:     $data['permissions'] ?? [],
            tenantId:        $data['tenant_id'] ?? null,
            createdAt:       $data['created_at'] ?? now()->toISOString(),
            updatedAt:       $data['updated_at'] ?? now()->toISOString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'description'      => $this->description,
            'created_by'       => $this->createdBy,
            'layout'           => $this->layout,
            'settings'         => $this->settings,
            'is_public'        => $this->isPublic,
            'public_token'     => $this->publicToken,
            'public_expires_at'=> $this->publicExpiresAt,
            'is_default'       => $this->isDefault,
            'widgets'          => $this->widgets->toArray(),
            'permissions'      => $this->permissions,
            'tenant_id'        => $this->tenantId,
            'created_at'       => $this->createdAt,
            'updated_at'       => $this->updatedAt,
        ];
    }
}
