<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mostafax\AnalyticsSuite\Security\SecurityManager;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'analytics-suite:sync-permissions
                            {--role= : Assign all permissions to this role}
                            {--guard=web : Guard name}';

    protected $description = 'Sync Analytics Suite permissions to the database';

    public function handle(): int
    {
        $guard = $this->option('guard');

        foreach (SecurityManager::PERMISSIONS as $name) {
            $group = $this->resolveGroup($name);
            DB::table('as_permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'guard_name' => $guard,
                    'group'      => $group,
                    'description'=> "Analytics Suite: {$name}",
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->info('Permissions synced: ' . count(SecurityManager::PERMISSIONS));

        // Spatie integration
        if (($role = $this->option('role')) && class_exists(\Spatie\Permission\Models\Role::class)) {
            $roleModel = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => $guard]);
            foreach (SecurityManager::PERMISSIONS as $perm) {
                $p = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => $guard]);
                if (!$roleModel->hasPermissionTo($p)) {
                    $roleModel->givePermissionTo($p);
                }
            }
            $this->info("All permissions assigned to role: {$role}");
        }

        return self::SUCCESS;
    }

    private function resolveGroup(string $name): string
    {
        return match (true) {
            str_contains($name, 'report')    => 'reports',
            str_contains($name, 'dashboard') => 'dashboards',
            str_contains($name, 'widget')    => 'widgets',
            default                          => 'admin',
        };
    }
}
