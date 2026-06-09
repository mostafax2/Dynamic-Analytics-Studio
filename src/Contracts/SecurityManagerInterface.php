<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface SecurityManagerInterface
{
    public function can(Authenticatable $user, string $permission): bool;

    public function canAny(Authenticatable $user, array $permissions): bool;

    public function canAll(Authenticatable $user, array $permissions): bool;

    /** Apply row-level security scope to a query builder. */
    public function applyRowScope(mixed $query, Authenticatable $user, string $model): mixed;

    /** Apply branch/department/tenant filters. */
    public function applyContextScope(mixed $query, Authenticatable $user): mixed;

    public function authorizeOr403(Authenticatable $user, string $permission): void;

    public function getPermissionsForUser(Authenticatable $user): array;

    public function allPermissions(): array;
}
