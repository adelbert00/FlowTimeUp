<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TimeSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReportController extends Controller
{
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

        $projectId = $request->get('project_id');
        $tagId = $request->get('tag_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $taskId = $request->get('task_id');
        $includeBillable = $request->boolean('include_billable', true);
        $includeNonBillable = $request->boolean('include_non_billable', true);

        $query = TimeSession::with(['task.project', 'task.tags'])
            ->whereHas('task', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereNotNull('end_time');

        if ($projectId) {
            $query->whereHas('task', function ($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        if ($tagId) {
            $query->whereHas('task.tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        if ($taskId) {
            $query->where('task_id', $taskId);
        }

        if ($startDate) {
            $query->whereDate('start_time', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('start_time', '<=', $endDate);
        }

        if (!$includeBillable && $includeNonBillable) {
            $query->where('is_billable', false);
        } elseif ($includeBillable && !$includeNonBillable) {
            $query->where('is_billable', true);
        }

        $sessions = $query->orderBy('start_time', 'desc')->get();

        if ($format === 'csv') {
            return $this->exportCsv($sessions);
        }

        return Inertia::render('Reports/Export', [
            'sessions' => $sessions->map(function ($session) {
                $duration = $session->start_time->diffInSeconds($session->end_time);
                $hourlyRate = $session->task->hourly_rate ?? $session->task->project?->hourly_rate ?? 0;
                $hours = $duration / 3600;
                $earnings = $session->is_billable ? round($hours * $hourlyRate, 2) : 0;
                
                return [
                    'id' => $session->id,
                    'task' => $session->task->title,
                    'project' => $session->task->project?->name ?? 'No project',
                    'tags' => $session->task->tags->pluck('name')->join(', ') ?: 'No tags',
                    'start_time' => $session->start_time->format('Y-m-d H:i:s'),
                    'end_time' => $session->end_time->format('Y-m-d H:i:s'),
                    'duration' => $this->formatDuration($duration),
                    'duration_seconds' => $duration,
                    'is_billable' => $session->is_billable,
                    'hourly_rate' => $hourlyRate,
                    'currency' => $session->task->currency ?? $session->task->project?->currency ?? 'USD',
                    'earnings' => $earnings,
                    'notes' => $session->notes ?? '',
                ];
            }),
            'summary' => $this->calculateSummary($sessions),
            'filters' => $request->only(['project_id', 'tag_id', 'start_date', 'end_date', 'task_id']),
        ]);
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

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Date',
                'Task',
                'Project',
                'Tags',
                'Start Time',
                'End Time',
                'Duration (HH:MM:SS)',
                'Duration (seconds)',
                'Billable',
                'Hourly Rate',
                'Currency',
                'Earnings',
                'Notes',
            ]);

            foreach ($sessions as $session) {
                $duration = $session->start_time->diffInSeconds($session->end_time);
                $tags = $session->task->tags->pluck('name')->join('; ') ?: '';
                $hourlyRate = $session->task->hourly_rate ?? $session->task->project?->hourly_rate ?? 0;
                $hours = $duration / 3600;
                $earnings = $session->is_billable ? round($hours * $hourlyRate, 2) : 0;
                $currency = $session->task->currency ?? $session->task->project?->currency ?? 'USD';

                fputcsv($file, [
                    $session->start_time->format('Y-m-d'),
                    $session->task->title,
                    $session->task->project?->name ?? 'No project',
                    $tags,
                    $session->start_time->format('Y-m-d H:i:s'),
                    $session->end_time->format('Y-m-d H:i:s'),
                    $this->formatDuration($duration),
                    $duration,
                    $session->is_billable ? 'Yes' : 'No',
                    $hourlyRate,
                    $currency,
                    $earnings,
                    $session->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    private function calculateSummary($sessions): array
    {
        $totalSeconds = 0;
        $billableSeconds = 0;
        $nonBillableSeconds = 0;
        $totalEarnings = 0;
        $byProject = [];
        $byTask = [];
        $byDate = [];

        foreach ($sessions as $session) {
            $duration = $session->start_time->diffInSeconds($session->end_time);
            $totalSeconds += $duration;
            
            $hourlyRate = $session->task->hourly_rate ?? $session->task->project?->hourly_rate ?? 0;
            $hours = $duration / 3600;
            
            if ($session->is_billable) {
                $billableSeconds += $duration;
                $totalEarnings += $hours * $hourlyRate;
            } else {
                $nonBillableSeconds += $duration;
            }
            
            $projectName = $session->task->project?->name ?? 'No project';
            $taskName = $session->task->title;
            $date = $session->start_time->format('Y-m-d');
            $currency = $session->task->currency ?? $session->task->project?->currency ?? 'USD';

            if (!isset($byProject[$projectName])) {
                $byProject[$projectName] = ['seconds' => 0, 'earnings' => 0, 'currency' => $currency];
            }
            $byProject[$projectName]['seconds'] += $duration;
            if ($session->is_billable) {
                $byProject[$projectName]['earnings'] += $hours * $hourlyRate;
            }

            $byTask[$taskName] = ($byTask[$taskName] ?? 0) + $duration;
            $byDate[$date] = ($byDate[$date] ?? 0) + $duration;
        }

        return [
            'total_time' => $this->formatDuration($totalSeconds),
            'total_seconds' => $totalSeconds,
            'total_sessions' => $sessions->count(),
            'billable_time' => $this->formatDuration($billableSeconds),
            'billable_seconds' => $billableSeconds,
            'non_billable_time' => $this->formatDuration($nonBillableSeconds),
            'non_billable_seconds' => $nonBillableSeconds,
            'total_earnings' => round($totalEarnings, 2),
            'by_project' => collect($byProject)->map(function ($data, $name) {
                return [
                    'name' => $name,
                    'duration' => $this->formatDuration($data['seconds']),
                    'seconds' => $data['seconds'],
                    'earnings' => round($data['earnings'], 2),
                    'currency' => $data['currency'],
                ];
            })->values(),
            'by_task' => collect($byTask)->map(function ($seconds, $name) {
                return [
                    'name' => $name,
                    'duration' => $this->formatDuration($seconds),
                    'seconds' => $seconds,
                ];
            })->sortByDesc('seconds')->take(10)->values(),
            'by_date' => collect($byDate)->map(function ($seconds, $date) {
                return [
                    'date' => $date,
                    'duration' => $this->formatDuration($seconds),
                    'seconds' => $seconds,
                ];
            })->sortByDesc('date')->values(),
        ];
    }

    public function data(Request $request)
    {
        $userId = $request->user()->id;

        $projectId = $request->get('project_id');
        $tagId = $request->get('tag_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $taskId = $request->get('task_id');

        $query = TimeSession::with(['task.project', 'task.tags'])
            ->whereHas('task', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereNotNull('end_time');

        if ($projectId) {
            $query->whereHas('task', function ($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        if ($tagId) {
            $query->whereHas('task.tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        if ($taskId) {
            $query->where('task_id', $taskId);
        }

        if ($startDate) {
            $query->whereDate('start_time', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('start_time', '<=', $endDate);
        }

        $sessions = $query->orderBy('start_time', 'desc')->get();

        return response()->json([
            'sessions' => $sessions->map(function ($session) {
                $duration = $session->start_time->diffInSeconds($session->end_time);
                $hourlyRate = $session->task->hourly_rate ?? $session->task->project?->hourly_rate ?? 0;
                $hours = $duration / 3600;
                $earnings = $session->is_billable ? round($hours * $hourlyRate, 2) : 0;
                
                return [
                    'id' => $session->id,
                    'task' => $session->task->title,
                    'project' => $session->task->project?->name ?? 'No project',
                    'tags' => $session->task->tags->pluck('name')->join(', ') ?: 'No tags',
                    'start_time' => $session->start_time->format('Y-m-d H:i:s'),
                    'end_time' => $session->end_time->format('Y-m-d H:i:s'),
                    'duration' => $this->formatDuration($duration),
                    'duration_seconds' => $duration,
                    'is_billable' => $session->is_billable,
                    'hourly_rate' => $hourlyRate,
                    'earnings' => $earnings,
                    'notes' => $session->notes ?? '',
                ];
            }),
            'summary' => $this->calculateSummary($sessions),
        ]);
    }
}
