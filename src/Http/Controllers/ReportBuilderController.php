<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mostafax\AnalyticsSuite\Http\Resources\ReportTemplateResource;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\ReportTemplateModel;
use Mostafax\AnalyticsSuite\Security\SecurityManager;
use Mostafax\ReportingEngine\Core\Engine\ReportEngine;

final class ReportBuilderController extends Controller
{
    public function __construct(
        private readonly ReportEngine    $engine,
        private readonly SecurityManager $security,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_reports');

        $query = ReportTemplateModel::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->input('search')}%");
        }
        if ($request->filled('category')) {
            $query->forCategory($request->input('category'));
        }
        if ($request->boolean('templates_only')) {
            $query->templates();
        }

        if ($request->user()) {
            $query->where('created_by', $request->user()->id);
        }

        $templates = $query->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ReportTemplateResource::collection($templates->items()),
            'meta' => [
                'total'        => $templates->total(),
                'current_page' => $templates->currentPage(),
                'last_page'    => $templates->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'create_reports');

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'data_source'  => 'required|string',
            'source_type'  => 'nullable|in:mysql,mongodb',
            'columns'      => 'nullable|array',
            'filters'      => 'nullable|array',
            'group_by'     => 'nullable|array',
            'order_by'     => 'nullable|array',
            'aggregations' => 'nullable|array',
            'joins'        => 'nullable|array',
            'settings'     => 'nullable|array',
            'is_template'  => 'nullable|boolean',
            'category'     => 'nullable|string',
            'tags'         => 'nullable|array',
        ]);
        $data['created_by'] = $request->user()?->id ?? 0;

        $template = ReportTemplateModel::create($data);

        return response()->json(new ReportTemplateResource($template), 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_reports');

        $template = ReportTemplateModel::findOrFail($id);

        return response()->json(new ReportTemplateResource($template));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'edit_reports');

        $template = ReportTemplateModel::findOrFail($id);
        $template->update($request->validate([
            'name'         => 'sometimes|string|max:255',
            'description'  => 'nullable|string',
            'data_source'  => 'sometimes|string',
            'columns'      => 'nullable|array',
            'filters'      => 'nullable|array',
            'group_by'     => 'nullable|array',
            'order_by'     => 'nullable|array',
            'aggregations' => 'nullable|array',
            'settings'     => 'nullable|array',
        ]));

        return response()->json(new ReportTemplateResource($template->fresh()));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'delete_reports');

        ReportTemplateModel::findOrFail($id)->delete();

        return response()->json(null, 204);
    }

    public function runPreview(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_reports');

        $dsl = $request->validate([
            'source'       => 'nullable|string',
            'table'        => 'required|string',
            'fields'       => 'nullable|array',
            'filters'      => 'nullable|array',
            'group_by'     => 'nullable|array',
            'order_by'     => 'nullable|array',
            'aggregations' => 'nullable|array',
            'joins'        => 'nullable|array',
            'pagination'   => 'nullable|array',
        ]);

        $dsl['source']     = $dsl['source'] ?: 'mysql';
        $dsl['pagination'] ??= ['page' => 1, 'per_page' => 25];

        $result = $this->engine->run($dsl, []);

        return response()->json([
            'rows'    => $result->data,
            'total'   => $result->total,
            'columns' => array_keys((array) ($result->data[0] ?? [])),
            'meta'    => array_merge((array) ($result->metadata ?? []), [
                'source'      => $dsl['source'],
                'table'       => $dsl['table'],
                'executed_at' => now()->toISOString(),
                'preview'     => true,
            ]),
        ]);
    }

    public function run(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_reports');

        $template = ReportTemplateModel::findOrFail($id);
        $dsl      = $template->toDslDefinition();

        // Merge runtime filters from request
        if ($request->filled('filters')) {
            $dsl['filters'] = array_merge($dsl['filters'] ?? [], $request->input('filters'));
        }
        if ($request->filled('pagination')) {
            $dsl['pagination'] = $request->input('pagination');
        }

        $result = $this->engine->run($dsl, $request->user()->roles ?? []);

        return response()->json([
            'rows'    => $result->data,
            'total'   => $result->total,
            'columns' => array_keys((array) ($result->data[0] ?? [])),
            'meta'    => [
                'report_id'    => $id,
                'source'       => $template->data_source,
                'source_type'  => $template->source_type,
                'executed_at'  => now()->toISOString(),
            ],
        ]);
    }

    public function clone(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'create_reports');

        $original = ReportTemplateModel::findOrFail($id);
        $clone    = $original->replicate(['created_at', 'updated_at', 'deleted_at']);
        $clone->name       = $request->input('name', $original->name . ' (Copy)');
        $clone->created_by = $request->user()?->id ?? 0;
        $clone->is_template = false;
        $clone->save();

        return response()->json(new ReportTemplateResource($clone), 201);
    }

    public function exportTemplate(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'export_reports');

        $template = ReportTemplateModel::findOrFail($id);

        return response()->json(['definition' => $template->toArray()]);
    }

    public function importTemplate(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'create_reports');

        $definition = $request->input('definition', []);
        unset($definition['id'], $definition['created_at'], $definition['updated_at'], $definition['deleted_at']);
        $definition['created_by'] = $request->user()?->id ?? 0;

        $template = ReportTemplateModel::create($definition);

        return response()->json(new ReportTemplateResource($template), 201);
    }
}
