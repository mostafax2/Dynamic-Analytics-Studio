<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Tests\Unit\Detection;

use Mostafax\AnalyticsSuite\Detection\ModelDetectionEngine;
use Orchestra\Testbench\TestCase;

class ModelDetectionEngineTest extends TestCase
{
    private ModelDetectionEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new ModelDetectionEngine();
    }

    public function test_detect_models_returns_collection(): void
    {
        $models = $this->engine->detectModels();
        $this->assertIsIterable($models);
    }

    public function test_detect_modules_returns_collection(): void
    {
        $modules = $this->engine->detectModules();
        $this->assertIsIterable($modules);
    }

    public function test_generate_widgets_returns_at_least_one_widget(): void
    {
        // Minimal DetectedModelDTO stub
        $dto = new \Mostafax\AnalyticsSuite\DTOs\DetectedModelDTO(
            class:          'App\Models\User',
            name:           'User',
            table:          'users',
            module:         'App',
            fillable:       ['name', 'email'],
            casts:          [],
            relationships:  [],
            columns:        ['id', 'name', 'email', 'created_at'],
            hasSoftDeletes: false,
            primaryKey:     'id',
            scopes:         collect([]),
        );

        $widgets = $this->engine->generateWidgets($dto);

        $this->assertGreaterThanOrEqual(1, $widgets->count());
    }

    public function test_generate_dashboard_returns_name_and_description(): void
    {
        $dto = new \Mostafax\AnalyticsSuite\DTOs\DetectedModelDTO(
            class: 'App\Models\User', name: 'User', table: 'users', module: 'App',
            fillable: [], casts: [], relationships: [], columns: [],
            hasSoftDeletes: false, primaryKey: 'id', scopes: collect([]),
        );

        $dashboard = $this->engine->generateDashboard($dto);

        $this->assertArrayHasKey('name', $dashboard);
        $this->assertArrayHasKey('description', $dashboard);
        $this->assertStringContainsString('User', $dashboard['name']);
    }
}
