<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Tests\Unit\Services;

use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Mostafax\AnalyticsSuite\Cache\AnalyticsCacheManager;
use Mostafax\AnalyticsSuite\Contracts\DashboardRepositoryInterface;
use Mostafax\AnalyticsSuite\DTOs\DashboardDTO;
use Mostafax\AnalyticsSuite\Services\DashboardService;
use PHPUnit\Framework\TestCase;

class DashboardServiceTest extends TestCase
{
    private DashboardRepositoryInterface&MockInterface $repository;
    private AnalyticsCacheManager&MockInterface        $cache;
    private DashboardService                           $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(DashboardRepositoryInterface::class);
        $this->cache      = Mockery::mock(AnalyticsCacheManager::class);
        $this->service    = new DashboardService($this->repository, $this->cache);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_find_returns_dto_from_cache(): void
    {
        $dto = $this->makeDto();

        $this->cache
            ->shouldReceive('rememberDashboard')
            ->once()
            ->with(1, Mockery::type('callable'))
            ->andReturn($dto);

        $result = $this->service->find(1);

        $this->assertSame($dto, $result);
    }

    public function test_create_stores_and_fires_event(): void
    {
        $dto = $this->makeDto();

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->andReturn($dto);

        $this->cache
            ->shouldReceive('invalidateUserDashboardList')
            ->once()
            ->with(42);

        $result = $this->service->create(['name' => 'Test'], 42);

        $this->assertInstanceOf(DashboardDTO::class, $result);
    }

    public function test_delete_invalidates_cache(): void
    {
        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->cache
            ->shouldReceive('invalidateDashboard')
            ->once()
            ->with(1);

        $result = $this->service->delete(1);

        $this->assertTrue($result);
    }

    public function test_clone_delegates_to_repository(): void
    {
        $dto = $this->makeDto();

        $this->repository
            ->shouldReceive('clone')
            ->once()
            ->with(1, 'New Name')
            ->andReturn($dto);

        $this->cache
            ->shouldReceive('invalidateUserDashboardList')
            ->once();

        $result = $this->service->clone(1, 'New Name', 42);

        $this->assertInstanceOf(DashboardDTO::class, $result);
    }

    private function makeDto(): DashboardDTO
    {
        return DashboardDTO::fromArray([
            'id'         => 1,
            'name'       => 'Test',
            'slug'       => 'test',
            'created_by' => 42,
            'widgets'    => [],
        ]);
    }
}
