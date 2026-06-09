<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mostafax\AnalyticsSuite\Http\Resources\ScheduledReportResource;
use Mostafax\AnalyticsSuite\Scheduling\ReportScheduler;
use Mostafax\AnalyticsSuite\Security\SecurityManager;

final class ScheduleController extends Controller
{
    public function __construct(
        private readonly ReportScheduler $scheduler,
        private readonly SecurityManager $security,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'schedule_reports');

        $list = $this->scheduler->listForUser($request->user()->id);

        return response()->json(['data' => ScheduledReportResource::collection($list)]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'schedule_reports');

        $data = $request->validate([
            'report_id'        => 'required|integer|exists:as_report_templates,id',
            'name'             => 'required|string|max:255',
            'frequency'        => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'cron_expression'  => 'nullable|string',
            'format'           => 'required|in:pdf,excel,csv',
            'delivery_methods' => 'required|array|min:1',
            'delivery_methods.*'=> 'in:email,notification,webhook',
            'recipients'       => 'nullable|array',
            'recipients.*'     => 'email',
            'webhook_url'      => 'nullable|url',
            'params'           => 'nullable|array',
        ]);

        $scheduled = $this->scheduler->schedule($data);

        return response()->json(new ScheduledReportResource($scheduled), 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'schedule_reports');

        $list = $this->scheduler->listForUser($request->user()->id);
        $item = $list->firstWhere(fn ($r) => $r->id == $id);

        if (!$item) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(new ScheduledReportResource($item));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'schedule_reports');

        $scheduled = $this->scheduler->update($id, $request->only([
            'name', 'frequency', 'cron_expression', 'format',
            'delivery_methods', 'recipients', 'webhook_url', 'params',
        ]));

        return response()->json(new ScheduledReportResource($scheduled));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'schedule_reports');

        $this->scheduler->cancel($id);

        return response()->json(null, 204);
    }

    public function pause(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'schedule_reports');

        $this->scheduler->pause($id);

        return response()->json(['message' => 'Scheduled report paused']);
    }

    public function resume(Request $request, int $id): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'schedule_reports');

        $this->scheduler->resume($id);

        return response()->json(['message' => 'Scheduled report resumed']);
    }
}
