<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mostafax\AnalyticsSuite\Providers\AnalyticsSuiteServiceProvider;
use Orchestra\Testbench\TestCase;

class DashboardApiTest extends TestCase
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
        $app['config']->set('analytics-suite.security.enforce_permissions', false);
    }

    public function test_index_returns_200(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->getJson('/api/analytics/dashboards');

        $response->assertStatus(200)->assertJsonStructure(['data', 'meta']);
    }

    public function test_store_creates_dashboard(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->postJson('/api/analytics/dashboards', [
            'name' => 'Test Dashboard',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.name', 'Test Dashboard');
    }

    public function test_destroy_deletes_dashboard(): void
    {
        $user = $this->makeUser();

        $created = $this->actingAs($user)->postJson('/api/analytics/dashboards', [
            'name' => 'To Delete',
        ])->json('data');

        $this->actingAs($user)->deleteJson("/api/analytics/dashboards/{$created['id']}")
            ->assertStatus(204);
    }

    private function makeUser(): object
    {
        return new class(1) {
            public function __construct(public int $id) {}
            public function getAuthIdentifier()  { return $this->id; }
            public function getAuthPassword()    { return ''; }
            public function getRememberToken()   { return null; }
            public function setRememberToken($v) {}
            public function getRememberTokenName() { return 'remember_token'; }
            public function can(string $p): bool { return true; }
        };
    }
}
