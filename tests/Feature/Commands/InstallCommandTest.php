<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Tests\Feature\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mostafax\AnalyticsSuite\Providers\AnalyticsSuiteServiceProvider;
use Orchestra\Testbench\TestCase;

class InstallCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [AnalyticsSuiteServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    public function test_install_command_runs_successfully(): void
    {
        $this->artisan('analytics-suite:install', [
            '--skip-detection' => true,
            '--skip-defaults'  => true,
        ])->assertSuccessful();
    }

    public function test_detect_models_command_runs(): void
    {
        $this->artisan('analytics-suite:detect-models')
            ->assertSuccessful();
    }

    public function test_sync_permissions_command_runs(): void
    {
        $this->artisan('analytics-suite:sync-permissions')
            ->assertSuccessful();
    }
}
