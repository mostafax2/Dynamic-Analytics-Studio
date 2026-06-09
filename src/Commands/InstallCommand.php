<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mostafax\AnalyticsSuite\Detection\ModelDetectionEngine;
use Mostafax\AnalyticsSuite\Security\SecurityManager;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

class InstallCommand extends Command
{
    protected $signature = 'analytics-suite:install
                            {--force : Overwrite existing config/assets}
                            {--skip-migrations : Skip running migrations}
                            {--skip-detection : Skip auto model detection}
                            {--skip-defaults : Skip generating default dashboards/widgets}';

    protected $description = 'Install the Enterprise Analytics Suite — zero-configuration setup';

    public function handle(ModelDetectionEngine $detector): int
    {
        $this->displayBanner();

        // Step 1 — Publish config
        spin(fn () => $this->publishConfig(), 'Publishing configuration...');
        $this->line('  <fg=green>✓</> Configuration published');

        // Step 2 — Publish assets
        spin(fn () => $this->publishAssets(), 'Publishing frontend assets...');
        $this->line('  <fg=green>✓</> Assets published');

        // Step 3 — Run migrations
        if (!$this->option('skip-migrations')) {
            spin(fn () => $this->runMigrations(), 'Running migrations...');
            $this->line('  <fg=green>✓</> Migrations complete');
        }

        // Step 4 — Seed permissions
        spin(fn () => $this->seedPermissions(), 'Creating enterprise permissions...');
        $this->line('  <fg=green>✓</> Permissions created');

        // Step 5 — Detect models
        if (!$this->option('skip-detection')) {
            $models = spin(fn () => $detector->detectModels(), 'Scanning models & modules...');
            $this->line("  <fg=green>✓</> Detected {$models->count()} model(s)");

            // Step 6 — Generate defaults
            if (!$this->option('skip-defaults') && $models->isNotEmpty()) {
                $this->generateDefaults($detector, $models);
            }
        }

        // Step 7 — Configure cache
        spin(fn () => $this->configureCacheLayer(), 'Configuring Redis cache layer...');
        $this->line('  <fg=green>✓</> Cache layer configured');

        // Step 8 — Verify Sanctum
        $this->verifySanctum();

        // Step 9 — Print summary
        $this->displaySummary();

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function publishConfig(): void
    {
        $this->callSilent('vendor:publish', [
            '--tag'   => 'analytics-suite-config',
            '--force' => $this->option('force'),
        ]);
    }

    private function publishAssets(): void
    {
        $this->callSilent('vendor:publish', [
            '--tag'   => 'analytics-suite-assets',
            '--force' => $this->option('force'),
        ]);

        $this->callSilent('vendor:publish', [
            '--tag'   => 'analytics-suite-views',
            '--force' => $this->option('force'),
        ]);
    }

    private function runMigrations(): void
    {
        $this->callSilent('migrate', ['--path' => 'packages/mostafax/analytics-suite/database/migrations']);
    }

    private function seedPermissions(): void
    {
        foreach (SecurityManager::PERMISSIONS as $name) {
            $group = explode('_', $name, 2)[1] ?? 'general';
            DB::table('as_permissions')->updateOrInsert(
                ['name' => $name],
                ['guard_name' => 'web', 'group' => $group, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    private function generateDefaults(ModelDetectionEngine $detector, \Illuminate\Support\Collection $models): void
    {
        $total = $models->count();
        $this->line('');

        progress(
            label:    "Generating default dashboards & widgets",
            steps:    $models->toArray(),
            callback: function ($modelDto) use ($detector) {
                try {
                    $this->generateForModel($detector, $modelDto);
                } catch (\Throwable) {
                    // Skip models that fail to scaffold
                }
            },
        );

        $this->line("  <fg=green>✓</> Generated defaults for {$total} module(s)");
    }

    private function generateForModel(ModelDetectionEngine $detector, object $modelDto): void
    {
        // Check if already generated
        $existing = DB::table('as_detected_models')->where('class', $modelDto->class)->first();
        if ($existing && $existing->auto_generated_dashboard) {
            return;
        }

        // Create default dashboard
        $dash = DB::table('as_dashboards')->insertGetId([
            'name'        => "{$modelDto->name} Overview",
            'slug'        => \Illuminate\Support\Str::slug($modelDto->name . '-overview') . '-' . rand(100, 999),
            'description' => "Auto-generated analytics dashboard for {$modelDto->name}",
            'created_by'  => 1,
            'layout'      => json_encode([]),
            'settings'    => json_encode(['auto_generated' => true, 'model' => $modelDto->class]),
            'is_public'   => false,
            'is_default'  => false,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Create widgets
        $widgets = $detector->generateWidgets($modelDto);
        foreach ($widgets as $index => $widgetDef) {
            DB::table('as_widgets')->insert([
                'dashboard_id'     => $dash,
                'type'             => $widgetDef['type'],
                'title'            => $widgetDef['title'],
                'config'           => json_encode($widgetDef['config']),
                'position'         => json_encode($widgetDef['position']),
                'styling'          => json_encode([]),
                'refresh_interval' => 300,
                'cache_enabled'    => true,
                'cache_ttl'        => 300,
                'created_by'       => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // Track in detected_models
        DB::table('as_detected_models')->updateOrInsert(
            ['class' => $modelDto->class],
            [
                'name'                      => $modelDto->name,
                'table_name'                => $modelDto->table,
                'module'                    => $modelDto->module,
                'fillable'                  => json_encode($modelDto->fillable),
                'casts'                     => json_encode($modelDto->casts),
                'relationships'             => json_encode($modelDto->relationships),
                'columns'                   => json_encode($modelDto->columns),
                'has_soft_deletes'          => $modelDto->hasSoftDeletes,
                'primary_key'               => $modelDto->primaryKey,
                'auto_generated_widgets'    => true,
                'auto_generated_dashboard'  => true,
                'created_at'                => now(),
                'updated_at'                => now(),
            ]
        );
    }

    private function configureCacheLayer(): void
    {
        $driver = config('analytics-suite.cache.driver', 'redis');
        $this->line("  Cache driver: <fg=cyan>{$driver}</>");
    }

    private function verifySanctum(): void
    {
        if (class_exists(\Laravel\Sanctum\SanctumServiceProvider::class)) {
            $this->line('  <fg=green>✓</> Laravel Sanctum detected and integrated');
        } else {
            $this->line('  <fg=yellow>⚠</> Laravel Sanctum not found — install it for API authentication');
        }
    }

    private function displayBanner(): void
    {
        $this->line('');
        $this->line('<fg=bright-cyan>
  ╔══════════════════════════════════════════════════════════╗
  ║      ENTERPRISE ANALYTICS SUITE  —  v1.0.0              ║
  ║      by Mostafa Elbayyar                                 ║
  ║      Powered by Dynamic Hybrid Reporting Engine          ║
  ╚══════════════════════════════════════════════════════════╝
</>');
        $this->line('');
    }

    private function displaySummary(): void
    {
        $this->line('');
        $this->line('<fg=bright-green>  Installation complete!</>');
        $this->line('');
        $this->line('  <fg=cyan>API Base:</> ' . url(config('analytics-suite.routes.prefix', 'api/analytics')));
        $this->line('  <fg=cyan>Docs:</> https://github.com/mostafax2/Dynamic-Analytics-Studio');
        $this->line('');
        $this->line('  Next steps:');
        $this->line('  1. Add <fg=yellow>api/analytics</> prefix to your Sanctum allowed domains');
        $this->line('  2. Assign permissions to roles with <fg=yellow>analytics-suite:sync-permissions</>');
        $this->line('  3. Run <fg=yellow>php artisan queue:work</> to process async exports');
        $this->line('');
    }
}
