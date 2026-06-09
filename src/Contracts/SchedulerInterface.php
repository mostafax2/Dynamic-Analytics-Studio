<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Contracts;

use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\DTOs\ScheduledReportDTO;

interface SchedulerInterface
{
    public function schedule(array $data): ScheduledReportDTO;

    public function update(int|string $id, array $data): ScheduledReportDTO;

    public function cancel(int|string $id): bool;

    public function pause(int|string $id): bool;

    public function resume(int|string $id): bool;

    public function listForUser(int|string $userId): Collection;

    public function getDueReports(): Collection;

    public function dispatch(int|string $scheduledReportId): bool;
}
