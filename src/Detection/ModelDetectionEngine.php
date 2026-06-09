<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Detection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mostafax\AnalyticsSuite\Contracts\DetectionEngineInterface;
use Mostafax\AnalyticsSuite\DTOs\DetectedModelDTO;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Finder\Finder;

final class ModelDetectionEngine implements DetectionEngineInterface
{
    private const EXCLUDED_PARENTS = [
        'Illuminate\Database\Eloquent\Model',
        'Illuminate\Foundation\Auth\User',
    ];

    public function detectModels(): Collection
    {
        $models = collect();

        foreach ($this->scanPaths() as $class) {
            try {
                $dto    = $this->introspect($class);
                $models->push($dto);
            } catch (\Throwable) {
                // Skip uninstantiable / abstract models
            }
        }

        return $models;
    }

    public function introspect(string $modelClass): DetectedModelDTO
    {
        /** @var Model $instance */
        $instance   = new $modelClass();
        $reflection = new ReflectionClass($modelClass);

        $table = $instance->getTable();

        // Columns via schema introspection
        $columns = [];
        try {
            $columns = Schema::getColumnListing($table);
        } catch (\Throwable) {}

        $casts         = $instance->getCasts();
        $fillable      = $instance->getFillable();
        $hasSoftDeletes = in_array(SoftDeletes::class, array_keys((new ReflectionClass($modelClass))->getTraits()), true)
            || $this->usesTrait($reflection, SoftDeletes::class);

        $relationships = $this->extractRelationships($reflection, $instance);
        $scopes        = $this->extractScopes($reflection);

        return new DetectedModelDTO(
            class:          $modelClass,
            name:           $reflection->getShortName(),
            table:          $table,
            module:         $this->resolveModule($modelClass),
            fillable:       $fillable,
            casts:          $casts,
            relationships:  $relationships,
            columns:        $columns,
            hasSoftDeletes: $hasSoftDeletes,
            primaryKey:     $instance->getKeyName(),
            scopes:         $scopes,
        );
    }

    public function detectModules(): Collection
    {
        $modules = collect();
        $modulePaths = config('analytics-suite.detection.module_paths', [base_path('Modules')]);

        foreach ($modulePaths as $basePath) {
            if (!is_dir($basePath)) {
                continue;
            }
            $dirs = glob($basePath . '/*', GLOB_ONLYDIR);
            foreach ((array) $dirs as $dir) {
                $moduleName = basename($dir);
                $modelPath  = $dir . '/Models';
                if (is_dir($modelPath)) {
                    $modules->push([
                        'name' => $moduleName,
                        'path' => $modelPath,
                    ]);
                }
            }
        }

        return $modules;
    }

    public function discoverAll(): Collection
    {
        return $this->detectModels();
    }

    public function generateWidgets(DetectedModelDTO $model): Collection
    {
        $widgets = collect();
        $label   = $model->label();

        // Total count KPI
        $widgets->push([
            'type'  => 'kpi_card',
            'title' => "Total {$label}",
            'config' => [
                'data_source'  => $model->table,
                'aggregation'  => 'count',
                'column'       => $model->primaryKey,
            ],
            'position' => ['x' => 0, 'y' => 0, 'w' => 3, 'h' => 2],
        ]);

        // Growth trend line chart (if has created_at)
        if (in_array('created_at', $model->columns, true)) {
            $widgets->push([
                'type'  => 'line_chart',
                'title' => "{$label} Growth",
                'config' => [
                    'data_source'  => $model->table,
                    'aggregation'  => 'count',
                    'column'       => $model->primaryKey,
                    'group_by'     => 'created_at',
                    'interval'     => 'month',
                ],
                'position' => ['x' => 3, 'y' => 0, 'w' => 6, 'h' => 3],
            ]);
        }

        // Data table widget
        $widgets->push([
            'type'  => 'data_table',
            'title' => "Recent {$label}",
            'config' => [
                'data_source'  => $model->table,
                'columns'      => array_slice($model->fillable, 0, 6),
                'order_by'     => [['column' => $model->primaryKey, 'direction' => 'desc']],
                'limit'        => 10,
            ],
            'position' => ['x' => 0, 'y' => 3, 'w' => 12, 'h' => 4],
        ]);

        return $widgets;
    }

    public function generateDashboard(DetectedModelDTO $model): array
    {
        $label = $model->label();
        return [
            'name'        => "{$label} Overview",
            'description' => "Auto-generated analytics dashboard for {$label}",
            'settings'    => [
                'model'  => $model->class,
                'source' => 'auto-generated',
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function scanPaths(): array
    {
        $found   = [];
        $paths   = array_merge(
            config('analytics-suite.detection.scan_paths', [app_path('Models')]),
            $this->collectModulePaths()
        );
        $excluded = config('analytics-suite.detection.excluded_models', []);

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $finder = Finder::create()->files()->name('*.php')->in($path);

            foreach ($finder as $file) {
                $class = $this->classFromFile($file->getRealPath());
                if ($class === null) {
                    continue;
                }
                if (!class_exists($class) || !is_subclass_of($class, Model::class)) {
                    continue;
                }
                $ref = new ReflectionClass($class);
                if ($ref->isAbstract()) {
                    continue;
                }
                $shortName = $ref->getShortName();
                if (in_array($shortName, $excluded, true)) {
                    continue;
                }
                $found[] = $class;
            }
        }

        return array_unique($found);
    }

    private function collectModulePaths(): array
    {
        $paths   = [];
        $modules = $this->detectModules();
        foreach ($modules as $module) {
            $paths[] = $module['path'];
        }
        return $paths;
    }

    private function classFromFile(string $filePath): ?string
    {
        $content   = file_get_contents($filePath);
        $namespace = null;
        $class     = null;

        if (preg_match('/namespace\s+([^;]+)/m', $content, $m)) {
            $namespace = trim($m[1]);
        }
        if (preg_match('/class\s+(\w+)/m', $content, $m)) {
            $class = trim($m[1]);
        }

        if ($namespace && $class) {
            return $namespace . '\\' . $class;
        }

        return null;
    }

    private function extractRelationships(ReflectionClass $ref, Model $instance): array
    {
        $rels = [];
        $relationTypes = [
            'hasOne', 'hasMany', 'belongsTo', 'belongsToMany',
            'hasManyThrough', 'hasOneThrough', 'morphTo', 'morphMany',
        ];

        foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class !== $ref->getName()) {
                continue;
            }
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }
            try {
                $result = $method->invoke($instance);
                foreach ($relationTypes as $type) {
                    $fqcn = 'Illuminate\\Database\\Eloquent\\Relations\\' . Str::studly($type);
                    if ($result instanceof $fqcn) {
                        $rels[] = [
                            'method'   => $method->getName(),
                            'type'     => $type,
                            'related'  => get_class($result->getRelated()),
                        ];
                        break;
                    }
                }
            } catch (\Throwable) {
                // Relation instantiation may require more setup; skip
            }
        }

        return $rels;
    }

    private function extractScopes(ReflectionClass $ref): Collection
    {
        return collect($ref->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(fn (ReflectionMethod $m) => str_starts_with($m->getName(), 'scope'))
            ->map(fn (ReflectionMethod $m) => Str::camel(substr($m->getName(), 5)))
            ->values();
    }

    private function resolveModule(string $modelClass): string
    {
        if (str_contains($modelClass, 'Modules\\')) {
            preg_match('/Modules\\\\(\w+)/', $modelClass, $m);
            return $m[1] ?? 'App';
        }
        return 'App';
    }

    private function usesTrait(ReflectionClass $ref, string $trait): bool
    {
        $traits = [];
        $current = $ref;
        while ($current) {
            $traits = array_merge($traits, array_keys($current->getTraits()));
            $current = $current->getParentClass() ?: null;
        }
        return in_array($trait, $traits, true);
    }
}
