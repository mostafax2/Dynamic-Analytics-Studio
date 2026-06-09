<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mostafax\AnalyticsSuite\Http\Requests\CreateDashboardRequest;
use Mostafax\AnalyticsSuite\Http\Requests\UpdateDashboardRequest;
use Mostafax\AnalyticsSuite\Http\Resources\DashboardResource;
use Mostafax\AnalyticsSuite\Security\SecurityManager;
use Mostafax\AnalyticsSuite\Services\DashboardService;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $service,
        private readonly SecurityManager  $security,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_dashboards');

        $dashboards = $this->service->paginate(
            $request->integer('per_page', 15),
            $request->only(['search', 'tenant_id', 'created_by'])
        );

        return response()->json([
            'data' => DashboardResource::collection($dashboards->items()),
            'meta' => [
                'current_page' => $dashboards->currentPage(),
                'last_page'    => $dashboards->lastPage(),
                'per_page'     => $dashboards->perPage(),
                'total'        => $dashboards->total(),
            ],
        ]);
    }

    public function store(CreateDashboardRequest $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'create_dashboards');

        $dashboard = $this->service->create($request->validated(), $request->user()->id);

        return response()->json(new DashboardResource($dashboard), 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'view_dashboards');

        $dashboard = $this->service->find($id);

        return response()->json(new DashboardResource($dashboard));
    }

    public function update(UpdateDashboardRequest $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'edit_dashboards');

        $dashboard = $this->service->update($id, $request->validated());

        return response()->json(new DashboardResource($dashboard));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'delete_dashboards');

        $this->service->delete($id);

        return response()->json(['message' => 'Dashboard deleted'], 204);
    }

    public function clone(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'create_dashboards');

        $name      = $request->input('name', 'Copy of Dashboard');
        $dashboard = $this->service->clone($id, $name, $request->user()->id);

        return response()->json(new DashboardResource($dashboard), 201);
    }

    public function layout(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'edit_dashboards');

        $layout = $request->input('layout', []);
        $this->service->updateLayout($id, $layout);

        return response()->json(['message' => 'Layout updated']);
    }

    public function share(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'edit_dashboards');

        $expiryDays = $request->integer('expiry_days', 7);
        $dashboard  = $this->service->enablePublicShare($id, $expiryDays);

        return response()->json([
            'public_url'       => url("/analytics/public/{$dashboard->publicToken}"),
            'public_token'     => $dashboard->publicToken,
            'public_expires_at'=> $dashboard->publicExpiresAt,
        ]);
    }

    public function unshare(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'edit_dashboards');

        $dashboard = $this->service->disablePublicShare($id);

        return response()->json(['message' => 'Public sharing disabled']);
    }

    public function export(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'export_reports');

        $definition = $this->service->export($id);

        return response()->json(['data' => $definition]);
    }

    public function import(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'create_dashboards');

        $definition = $request->input('definition', []);
        $dashboard  = $this->service->import($definition, $request->user()->id);

        return response()->json(new DashboardResource($dashboard), 201);
    }

    public function showPublic(string $token): JsonResponse
    {
        $dashboard = $this->service->findPublic($token);
        return response()->json(new DashboardResource($dashboard));
    }
}
