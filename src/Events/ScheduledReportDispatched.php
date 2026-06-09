<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Mostafax\AnalyticsSuite\DTOs\ScheduledReportDTO;

final class ScheduledReportDispatched
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ScheduledReportDTO $scheduledReport) {}
}
