<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Commands;

use Illuminate\Console\Command;
use Mostafax\AnalyticsSuite\Detection\ModelDetectionEngine;

class DetectModelsCommand extends Command
{
    protected $signature = 'analytics-suite:detect-models
                            {--generate-widgets : Auto-generate widget definitions}
                            {--generate-dashboards : Auto-generate default dashboards}';

    protected $description = 'Detect application models and generate Analytics Suite resources';

    public function handle(ModelDetectionEngine $detector): int
    {
        $this->info('Scanning application models & modules...');

        $models = $detector->detectModels();

        if ($models->isEmpty()) {
            $this->warn('No models detected. Ensure scan paths are configured correctly.');
            return self::SUCCESS;
        }

        $this->table(
            ['Class', 'Name', 'Table', 'Module', 'Soft Deletes', 'Relations'],
            $models->map(fn ($m) => [
                $m->class,
                $m->name,
                $m->table,
                $m->module,
                $m->hasSoftDeletes ? 'Yes' : 'No',
                count($m->relationships),
            ])->toArray()
        );

        if ($this->option('generate-widgets')) {
            foreach ($models as $model) {
                $widgets = $detector->generateWidgets($model);
                $this->line("  Generated {$widgets->count()} widget(s) for {$model->name}");
            }
        }

        if ($this->option('generate-dashboards')) {
            foreach ($models as $model) {
                $dashboard = $detector->generateDashboard($model);
                $this->line("  Dashboard template created for {$model->name}: {$dashboard['name']}");
            }
        }

        $this->info("Detection complete. Found {$models->count()} model(s).");

        return self::SUCCESS;
    }
}
