<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mostafax\AnalyticsSuite\Analytics\AnalyticsEngine;
use Mostafax\AnalyticsSuite\Security\SecurityManager;

final class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsEngine $engine,
        private readonly SecurityManager $security,
    ) {}

    public function modules(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'manage_analytics');

        return response()->json(['data' => $this->engine->getAvailableModules()]);
    }

    public function stats(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_reports');

        $model  = $request->input('model');
        $config = $request->input('config', []);

        $result = $this->engine->computeStats($model, $config);

        return response()->json($result->toArray());
    }

    public function summary(Request $request, string $module): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_reports');

        $result = $this->engine->generateSummary($module);

        return response()->json($result->toArray());
    }

    public function dashboardData(Request $request, int $dashboardId): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_dashboards');

        $params  = $request->only(['filters', 'date_range']);
        $results = $this->engine->executeDashboard($dashboardId, $params);

        return response()->json(['data' => $results->map->toArray()]);
    }

    public function invalidateCache(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'manage_analytics');

        $type = $request->input('type', 'dashboard');
        $id   = $request->input('id');

        if ($type === 'widget' && $id) {
            $this->engine->refreshCache($id);
        } elseif ($type === 'dashboard' && $id) {
            $this->engine->invalidateDashboardCache($id);
        }

        return response()->json(['message' => 'Cache invalidated']);
    }
}
