<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mostafax\AnalyticsSuite\Export\ExportManager;
use Mostafax\AnalyticsSuite\Infrastructure\Persistence\Models\ExportJobModel;
use Mostafax\AnalyticsSuite\Security\SecurityManager;

final class ExportController extends Controller
{
    public function __construct(
        private readonly ExportManager   $manager,
        private readonly SecurityManager $security,
    ) {}

    public function queueExport(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'export_reports');

        $data = $request->validate([
            'type'         => 'required|in:report,widget,dashboard',
            'resource_id'  => 'required|integer',
            'format'       => 'required|in:pdf,excel,csv,json',
            'params'       => 'nullable|array',
            'notify_email' => 'nullable|email',
        ]);

        $jobId = $this->manager->queueExport(
            $data['type'],
            $data['resource_id'],
            $data['format'],
            $data['params'] ?? [],
            $data['notify_email'] ?? null,
        );

        return response()->json([
            'job_id'   => $jobId,
            'message'  => 'Export queued successfully',
            'poll_url' => route('analytics.export.status', $jobId),
        ], 202);
    }

    public function status(Request $request, int $jobId): JsonResponse
    {
        $job = ExportJobModel::where('id', $jobId)
            ->where('created_by', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'id'        => $job->id,
            'status'    => $job->status,
            'filename'  => $job->filename,
            'rows'      => $job->rows,
            'size'      => $job->size_bytes,
            'error'     => $job->error,
            'completed' => $job->completed_at?->toISOString(),
        ]);
    }

    public function formats(): JsonResponse
    {
        return response()->json(['data' => $this->manager->supportedFormats()]);
    }

    public function history(Request $request): JsonResponse
    {
        $this->security->authorizeOr403($request->user(), 'export_reports');

        $jobs = ExportJobModel::where('created_by', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json(['data' => $jobs->items(), 'total' => $jobs->total()]);
    }
}
