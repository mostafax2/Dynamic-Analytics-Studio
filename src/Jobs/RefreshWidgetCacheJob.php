<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mostafax\AnalyticsSuite\Analytics\AnalyticsEngine;

final class RefreshWidgetCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(private readonly int $widgetId) {}

    public function handle(AnalyticsEngine $engine): void
    {
        $engine->refreshCache($this->widgetId);
        $engine->executeWidget($this->widgetId);  // re-warms cache
    }
}
