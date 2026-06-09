<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Security;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Mostafax\AnalyticsSuite\Contracts\SecurityManagerInterface;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\RlsPolicyModel;

final class SecurityManager implements SecurityManagerInterface
{
    /** Enterprise permission set */
    public const PERMISSIONS = [
        // Reports
        'view_reports',   'create_reports',  'edit_reports',
        'delete_reports', 'export_reports',  'schedule_reports',
        // Dashboards
        'view_dashboards',   'create_dashboards',
        'edit_dashboards',   'delete_dashboards',
        // Widgets
        'create_widgets', 'edit_widgets', 'delete_widgets',
        // Admin
        'manage_analytics',
    ];

    private bool $enforce;

    public function __construct()
    {
        $this->enforce = (bool) config('analytics-suite.security.enforce_permissions', true);
    }

    public function can(Authenticatable $user, string $permission): bool
    {
        if (!$this->enforce) {
            return true;
        }

        // Super-admin bypass
        if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
            return true;
        }

        // Spatie permission integration
        if (method_exists($user, 'can')) {
            return $user->can($permission);
        }

        // Fallback: direct table lookup
        return $this->checkDirectPermission($user, $permission);
    }

    public function canAny(Authenticatable $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($user, $permission)) {
                return true;
            }
        }
        return false;
    }

    public function canAll(Authenticatable $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->can($user, $permission)) {
                return false;
            }
        }
        return true;
    }

    public function applyRowScope(mixed $query, Authenticatable $user, string $model): mixed
    {
        if (!config('analytics-suite.security.row_level_security', true)) {
            return $query;
        }

        $policies = RlsPolicyModel::where('model', $model)
            ->where('is_active', true)
            ->get();

        foreach ($policies as $policy) {
            $value = $this->resolveValue($policy->value_source, $policy->value_key, $user);
            if ($value !== null) {
                $query->where($policy->column, $policy->operator, $value);
            }
        }

        return $query;
    }

    public function applyContextScope(mixed $query, Authenticatable $user): mixed
    {
        if (config('analytics-suite.security.tenant_isolation') && isset($user->tenant_id)) {
            $col = config('analytics-suite.security.tenant_column', 'tenant_id');
            $query->where($col, $user->tenant_id);
        }

        return $query;
    }

    public function authorizeOr403(Authenticatable $user, string $permission): void
    {
        if (!$this->can($user, $permission)) {
            throw new HttpResponseException(
                response()->json(['message' => "Unauthorized: {$permission} required"], 403)
            );
        }
    }

    public function getPermissionsForUser(Authenticatable $user): array
    {
        if (method_exists($user, 'getAllPermissions')) {
            return $user->getAllPermissions()->pluck('name')->toArray();
        }

        return DB::table('as_user_permissions as up')
            ->join('as_permissions as p', 'p.id', '=', 'up.permission_id')
            ->where('up.user_id', $user->getAuthIdentifier())
            ->where('up.granted', true)
            ->pluck('p.name')
            ->toArray();
    }

    public function allPermissions(): array
    {
        return self::PERMISSIONS;
    }

    // -------------------------------------------------------------------------

    private function checkDirectPermission(Authenticatable $user, string $permission): bool
    {
        return DB::table('as_user_permissions as up')
            ->join('as_permissions as p', 'p.id', '=', 'up.permission_id')
            ->where('up.user_id', $user->getAuthIdentifier())
            ->where('p.name', $permission)
            ->where('up.granted', true)
            ->exists();
    }

    private function resolveValue(string $source, ?string $key, Authenticatable $user): mixed
    {
        return match ($source) {
            'auth_user' => $key ? ($user->{$key} ?? null) : null,
            'config'    => $key ? config($key) : null,
            'static'    => $key,
            default     => null,
        };
    }
}
