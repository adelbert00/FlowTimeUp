<?php

namespace App\Services;

use App\Models\TimeSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    public function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function getSummary(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $connectionType = config('database.default');

        // Define duration expression based on DB driver
        $durationExpr = $connectionType === 'pgsql'
            ? 'EXTRACT(EPOCH FROM (time_sessions.end_time - time_sessions.start_time))'
            : 'TIMESTAMPDIFF(SECOND, time_sessions.start_time, time_sessions.end_time)';

        $query = TimeSession::query()
            ->join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('time_sessions.user_id', $userId)
            ->whereNotNull('time_sessions.end_time');

        if ($startDate) {
            $query->where('time_sessions.start_time', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->where('time_sessions.start_time', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Global stats
        $stats = (clone $query)
            ->selectRaw("
                COUNT(*) as total_sessions,
                SUM($durationExpr) as total_seconds,
                SUM(CASE WHEN time_sessions.is_billable = true THEN $durationExpr ELSE 0 END) as billable_seconds,
                SUM(CASE WHEN time_sessions.is_billable = true THEN ($durationExpr / 3600.0) * COALESCE(time_sessions.billable_rate, tasks.hourly_rate, 0) ELSE 0 END) as total_earnings
            ")
            ->first();

        $totalSeconds = (int) ($stats->total_seconds ?? 0);
        $billableSeconds = (int) ($stats->billable_seconds ?? 0);

        // Group by Project
        $byProject = (clone $query)
            ->selectRaw("
                projects.id,
                COALESCE(projects.name, 'No Project') as name,
                SUM($durationExpr) as seconds,
                SUM(CASE WHEN time_sessions.is_billable = true THEN ($durationExpr / 3600.0) * COALESCE(time_sessions.billable_rate, tasks.hourly_rate, 0) ELSE 0 END) as earnings,
                COALESCE(tasks.currency, 'USD') as currency
            ")
            ->groupBy('projects.id', 'projects.name', 'tasks.currency')
            ->get()
            ->map(fn($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'duration' => $this->formatDuration($row->seconds),
                'seconds' => (int) $row->seconds,
                'earnings' => round((float) $row->earnings, 2),
                'currency' => $row->currency,
            ]);

        // Group by Tag
        $byTag = TimeSession::query()
            ->join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->join('tag_task', 'tasks.id', '=', 'tag_task.task_id')
            ->join('tags', 'tag_task.tag_id', '=', 'tags.id')
            ->where('time_sessions.user_id', $userId)
            ->whereNotNull('time_sessions.end_time');

        if ($startDate) {
            $byTag->where('time_sessions.start_time', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $byTag->where('time_sessions.start_time', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $byTag = $byTag
            ->selectRaw("
                tags.id,
                tags.name,
                SUM($durationExpr) as seconds
            ")
            ->groupBy('tags.id', 'tags.name')
            ->get()
            ->map(fn($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'duration' => $this->formatDuration($row->seconds),
                'seconds' => (int) $row->seconds,
            ])
            ->sortByDesc('seconds')
            ->values();

        // Group by Task
        $byTask = (clone $query)
            ->selectRaw("
                tasks.id,
                tasks.title as name,
                SUM($durationExpr) as seconds
            ")
            ->groupBy('tasks.id', 'tasks.title')
            ->orderByDesc('seconds')
            ->limit(10)
            ->get()
            ->map(fn($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'duration' => $this->formatDuration($row->seconds),
                'seconds' => (int) $row->seconds,
            ]);

        // Group by Date
        $dateColumn = $connectionType === 'pgsql' ? 'time_sessions.start_time::date' : 'DATE(time_sessions.start_time)';
        $byDate = (clone $query)
            ->selectRaw("
                $dateColumn as date,
                SUM($durationExpr) as seconds
            ")
            ->groupBy('date')
            ->orderByDesc('date')
            ->get()
            ->map(fn($row) => [
                'date' => $row->date,
                'duration' => $this->formatDuration($row->seconds),
                'seconds' => (int) $row->seconds,
            ]);

        return [
            'total_time' => $this->formatDuration($totalSeconds),
            'total_seconds' => $totalSeconds,
            'total_sessions' => (int) ($stats->total_sessions ?? 0),
            'billable_time' => $this->formatDuration($billableSeconds),
            'billable_seconds' => $billableSeconds,
            'non_billable_time' => $this->formatDuration($totalSeconds - $billableSeconds),
            'non_billable_seconds' => $totalSeconds - $billableSeconds,
            'total_earnings' => round((float) ($stats->total_earnings ?? 0), 2),
            'by_project' => $byProject,
            'by_tag' => $byTag,
            'by_task' => $byTask,
            'by_date' => $byDate,
        ];
    }

    public function calculateSummary(Collection $sessions): array
    {
        $totalSeconds = 0;
        $billableSeconds = 0;
        $totalEarnings = 0;
        $byProject = [];
        $byTask = [];
        $byDate = [];

        foreach ($sessions as $session) {
            $duration = $session->start_time->diffInSeconds($session->end_time);
            $totalSeconds += $duration;

            $hourlyRate = $session->billable_rate ?? $session->task->hourly_rate ?? 0;
            $hours = $duration / 3600;

            if ($session->is_billable) {
                $billableSeconds += $duration;
                $totalEarnings += $hours * $hourlyRate;
            }

            $projectName = $session->task->project?->name ?? 'No project';
            $taskName = $session->task->title;
            $date = $session->start_time->format('Y-m-d');

            if (!isset($byProject[$projectName])) {
                $byProject[$projectName] = ['seconds' => 0, 'earnings' => 0, 'currency' => $session->task->currency ?? 'USD'];
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
            'non_billable_time' => $this->formatDuration($totalSeconds - $billableSeconds),
            'non_billable_seconds' => $totalSeconds - $billableSeconds,
            'total_earnings' => round($totalEarnings, 2),
            'by_project' => collect($byProject)->map(fn($data, $name) => [
                'name' => $name,
                'duration' => $this->formatDuration($data['seconds']),
                'seconds' => $data['seconds'],
                'earnings' => round($data['earnings'], 2),
                'currency' => $data['currency'],
            ])->values(),
            'by_task' => collect($byTask)->map(fn($seconds, $name) => [
                'name' => $name,
                'duration' => $this->formatDuration($seconds),
                'seconds' => $seconds,
            ])->sortByDesc('seconds')->take(10)->values(),
            'by_date' => collect($byDate)->map(fn($seconds, $date) => [
                'date' => $date,
                'duration' => $this->formatDuration($seconds),
                'seconds' => $seconds,
            ])->sortByDesc('date')->values(),
        ];
    }

    public function formatSessionForExport($session): array
    {
        $duration = $session->start_time->diffInSeconds($session->end_time);
        $hourlyRate = $session->billable_rate ?? $session->task->hourly_rate ?? 0;
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
    }
}
