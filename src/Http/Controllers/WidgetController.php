<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mostafax\AnalyticsSuite\Analytics\AnalyticsEngine;
use Mostafax\AnalyticsSuite\Http\Resources\WidgetResource;
use Mostafax\AnalyticsSuite\Security\SecurityManager;
use Mostafax\AnalyticsSuite\Services\WidgetService;

final class WidgetController extends Controller
{
    public function __construct(
        private readonly WidgetService   $service,
        private readonly AnalyticsEngine $engine,
        private readonly SecurityManager $security,
    ) {}

    public function index(Request $request, int $dashboardId): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_dashboards');

        $widgets = $this->service->listByDashboard($dashboardId);

        return response()->json(['data' => WidgetResource::collection($widgets)]);
    }

    public function store(Request $request, int $dashboardId): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'create_widgets');

        $data                = $request->validate([
            'type'             => 'required|string',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'config'           => 'nullable|array',
            'position'         => 'nullable|array',
            'styling'          => 'nullable|array',
            'refresh_interval' => 'nullable|integer|min:0',
            'cache_enabled'    => 'nullable|boolean',
            'cache_ttl'        => 'nullable|integer|min:0',
            'report_id'        => 'nullable|integer',
            'report_params'    => 'nullable|array',
        ]);
        $data['dashboard_id'] = $dashboardId;

        $widget = $this->service->create($data, $request->user()?->id);

        return response()->json(new WidgetResource($widget), 201);
    }

    public function show(Request $request, int $dashboardId, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_dashboards');

        $widget = $this->service->find($id);

        return response()->json(new WidgetResource($widget));
    }

    public function update(Request $request, int $dashboardId, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'edit_widgets');

        $data   = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'config'           => 'sometimes|array',
            'position'         => 'sometimes|array',
            'styling'          => 'sometimes|array',
            'refresh_interval' => 'sometimes|integer|min:0',
            'cache_enabled'    => 'sometimes|boolean',
            'cache_ttl'        => 'sometimes|integer|min:0',
        ]);
        $widget = $this->service->update($id, $data);

        return response()->json(new WidgetResource($widget));
    }

    public function destroy(Request $request, int $dashboardId, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'delete_widgets');

        $this->service->delete($id);

        return response()->json(null, 204);
    }

    public function data(Request $request, int $dashboardId, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_dashboards');

        $params = $request->only(['filters', 'date_range', 'params']);
        $result = $this->engine->executeWidget($id, $params);

        return response()->json($result->toArray());
    }

    public function refresh(Request $request, int $dashboardId, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_dashboards');

        $this->engine->refreshCache($id);
        $result = $this->engine->executeWidget($id);

        return response()->json($result->toArray());
    }

    public function types(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->service->availableTypes()]);
    }
}
