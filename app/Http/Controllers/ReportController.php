<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tag;
use App\Models\TimeSession;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function index(Request $request): InertiaResponse
    {
        $userId = $request->user()->id;

        return Inertia::render('Reports/Index', [
            'projects' => Project::where('user_id', $userId)->get(),
            'tags' => Tag::where('user_id', $userId)->get(),
        ]);
    }

    public function export(Request $request)
    {
        $userId = $request->user()->id;
        $format = $request->get('format', 'csv');

        $sessions = $this->buildSessionQuery($request, $userId)->get();

        if ($format === 'csv') {
            return $this->exportCsv($sessions);
        }

        return Inertia::render('Reports/Export', [
            'sessions' => $sessions->map(fn($s) => $this->reportService->formatSessionForExport($s)),
            'summary' => $this->reportService->calculateSummary($sessions),
            'filters' => $request->only(['project_id', 'tag_id', 'start_date', 'end_date', 'task_id']),
        ]);
    }

    public function data(Request $request)
    {
        $userId = $request->user()->id;

        $sessions = $this->buildSessionQuery($request, $userId)->get();

        return response()->json([
            'sessions' => $sessions->map(fn($s) => $this->reportService->formatSessionForExport($s)),
            'summary' => $this->reportService->calculateSummary($sessions),
        ]);
    }

    private function buildSessionQuery(Request $request, int $userId)
    {
        $query = TimeSession::with(['task.project', 'task.tags'])
            ->whereHas('task', fn($q) => $q->where('user_id', $userId))
            ->whereNotNull('end_time');

        if ($projectId = $request->get('project_id')) {
            $query->whereHas('task', fn($q) => $q->where('project_id', $projectId));
        }

        if ($tagId = $request->get('tag_id')) {
            $query->whereHas('task.tags', fn($q) => $q->where('tags.id', $tagId));
        }

        if ($taskId = $request->get('task_id')) {
            $query->where('task_id', $taskId);
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('start_time', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->whereDate('start_time', '<=', $endDate);
        }

        $includeBillable = $request->boolean('include_billable', true);
        $includeNonBillable = $request->boolean('include_non_billable', true);

        if (!$includeBillable && $includeNonBillable) {
            $query->where('is_billable', false);
        } elseif ($includeBillable && !$includeNonBillable) {
            $query->where('is_billable', true);
        }

        return $query->orderBy('start_time', 'desc');
    }

    private function exportCsv($sessions)
    {
        $filename = 'flowtimeup-report-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($sessions) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Date', 'Task', 'Project', 'Tags',
                'Start Time', 'End Time', 'Duration (HH:MM:SS)', 'Duration (seconds)',
                'Billable', 'Hourly Rate', 'Currency', 'Earnings', 'Notes',
            ]);

            foreach ($sessions as $session) {
                $data = $this->reportService->formatSessionForExport($session);
                fputcsv($file, [
                    $session->start_time->format('Y-m-d'),
                    $data['task'],
                    $data['project'],
                    $session->task->tags->pluck('name')->join('; ') ?: '',
                    $data['start_time'],
                    $data['end_time'],
                    $data['duration'],
                    $data['duration_seconds'],
                    $session->is_billable ? 'Yes' : 'No',
                    $data['hourly_rate'],
                    $data['currency'],
                    $data['earnings'],
                    $data['notes'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
