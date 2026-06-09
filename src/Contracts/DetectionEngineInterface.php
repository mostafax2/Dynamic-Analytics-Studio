<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Contracts;

use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\DTOs\DetectedModelDTO;

interface DetectionEngineInterface
{
    /** Scan all configured paths and return detected models. */
    public function detectModels(): Collection;

    /** Introspect a single model class and return its metadata. */
    public function introspect(string $modelClass): DetectedModelDTO;

    /** Detect modules (e.g. Laravel Modules package). */
    public function detectModules(): Collection;

    /** Return all detected data sources (models + modules merged). */
    public function discoverAll(): Collection;

    /** Generate default widget definitions for a detected model. */
    public function generateWidgets(DetectedModelDTO $model): Collection;

    /** Generate a default dashboard for a detected model. */
    public function generateDashboard(DetectedModelDTO $model): array;
}
