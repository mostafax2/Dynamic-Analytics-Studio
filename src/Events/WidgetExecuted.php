<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Mostafax\AnalyticsSuite\DTOs\WidgetDataDTO;

final class WidgetExecuted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly WidgetDataDTO $result) {}
}
