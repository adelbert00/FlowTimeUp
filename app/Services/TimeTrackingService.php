<?php

namespace App\Services;

use App\Models\TimeSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TimeTrackingService
{
    public function getDashboardStats(int $userId, string $period): array
    {
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();

        $periodStats = Cache::remember("dashboard:{$userId}:{$period}", 300, function () use ($userId, $startDate, $endDate) {
            return [
                'total_seconds' => $this->calculateTotalSeconds($userId, $startDate, $endDate),
                'monthly_data' => $this->getMonthlyBreakdown($userId, $startDate, $endDate),
                'project_breakdown' => $this->getProjectBreakdown($userId, $startDate, $endDate),
            ];
        });

        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $todaySeconds = $this->sumSecondsFromQuery(
            TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
                ->where('tasks.user_id', $userId)
                ->whereDate('time_sessions.start_time', $today)
                ->whereNotNull('time_sessions.end_time')
        );

        $weeklySeconds = $this->sumSecondsFromQuery(
            TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
                ->where('tasks.user_id', $userId)
                ->whereBetween('time_sessions.start_time', [$weekStart, $weekEnd])
                ->whereNotNull('time_sessions.end_time')
        );

        return array_merge($periodStats, [
            'today_seconds' => $todaySeconds,
            'weekly_seconds' => $weeklySeconds,
        ]);
    }

    public function getMonthlyBreakdown(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $rows = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('tasks.user_id', $userId)
            ->whereBetween('time_sessions.start_time', [$startDate, $endDate])
            ->whereNotNull('time_sessions.end_time')
            ->select(
                DB::raw("TO_CHAR(time_sessions.start_time, 'YYYY-MM') as month_key"),
                'tasks.project_id',
                'projects.name as project_name',
                'projects.color as project_color',
                DB::raw('SUM(EXTRACT(EPOCH FROM (time_sessions.end_time - time_sessions.start_time)))::bigint as total_seconds')
            )
            ->groupBy('month_key', 'tasks.project_id', 'projects.name', 'projects.color')
            ->orderBy('month_key')
            ->get();

        $byMonth = [];
        foreach ($rows as $row) {
            $key = $row->month_key;
            if (!isset($byMonth[$key])) {
                $byMonth[$key] = [
                    'month' => Carbon::createFromFormat('Y-m', $key)->format('M'),
                    'month_full' => Carbon::createFromFormat('Y-m', $key)->format('F'),
                    'year' => Carbon::createFromFormat('Y-m', $key)->format('Y'),
                    'total_seconds' => 0,
                    'projects' => [],
                ];
            }
            $byMonth[$key]['total_seconds'] += $row->total_seconds;
            $byMonth[$key]['projects'][] = [
                'id' => $row->project_id ?? 0,
                'name' => $row->project_name ?? 'Without project',
                'color' => $row->project_color ?? '#9CA3AF',
                'seconds' => (int) $row->total_seconds,
            ];
        }

        // Fill in months with no sessions
        $months = [];
        $current = $startDate->copy()->startOfMonth();
        while ($current <= $endDate) {
            $key = $current->format('Y-m');
            if (isset($byMonth[$key])) {
                $entry = $byMonth[$key];
                $entry['formatted_time'] = $this->formatTime($entry['total_seconds']);
                $months[] = $entry;
            } else {
                $months[] = [
                    'month' => $current->format('M'),
                    'month_full' => $current->format('F'),
                    'year' => $current->format('Y'),
                    'total_seconds' => 0,
                    'formatted_time' => '00:00:00',
                    'projects' => [],
                ];
            }
            $current->addMonth();
        }

        return $months;
    }

    public function getProjectBreakdown(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $rows = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('tasks.user_id', $userId)
            ->whereBetween('time_sessions.start_time', [$startDate, $endDate])
            ->whereNotNull('time_sessions.end_time')
            ->select(
                'tasks.project_id',
                'projects.name as project_name',
                'projects.color as project_color',
                DB::raw('SUM(EXTRACT(EPOCH FROM (time_sessions.end_time - time_sessions.start_time)))::bigint as total_seconds')
            )
            ->groupBy('tasks.project_id', 'projects.name', 'projects.color')
            ->orderByDesc('total_seconds')
            ->get();

        $grandTotal = $rows->sum('total_seconds');

        return $rows->map(function ($row) use ($grandTotal) {
            $seconds = (int) $row->total_seconds;
            return [
                'id' => $row->project_id ?? 0,
                'name' => $row->project_name ?? 'Without project',
                'color' => $row->project_color ?? '#9CA3AF',
                'seconds' => $seconds,
                'formatted_time' => $this->formatTime($seconds),
                'percentage' => $grandTotal > 0 ? round(($seconds / $grandTotal) * 100, 2) : 0,
            ];
        })->toArray();
    }

    public function calculateTotalSeconds(int $userId, Carbon $startDate, ?Carbon $endDate = null): int
    {
        $query = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->where('tasks.user_id', $userId)
            ->where('time_sessions.start_time', '>=', $startDate)
            ->whereNotNull('time_sessions.end_time');

        if ($endDate) {
            $query->where('time_sessions.start_time', '<=', $endDate);
        }

        return (int) $this->sumSecondsFromQuery($query);
    }

    public function getRecentActivities(int $userId, int $limit = 10): array
    {
        $sessions = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('tasks.user_id', $userId)
            ->whereNotNull('time_sessions.end_time')
            ->select(
                'time_sessions.id',
                'time_sessions.start_time',
                'time_sessions.end_time',
                'tasks.title as task_title',
                'tasks.project_id',
                'projects.name as project_name',
                'projects.color as project_color',
                DB::raw('EXTRACT(EPOCH FROM (time_sessions.end_time - time_sessions.start_time))::bigint as duration_seconds')
            )
            ->orderBy('time_sessions.start_time', 'desc')
            ->take(50)
            ->get();

        $activities = [];

        foreach ($sessions as $session) {
            $date = Carbon::parse($session->start_time)->format('Y-m-d');

            if (!isset($activities[$date])) {
                $activities[$date] = [
                    'date' => $date,
                    'formatted_date' => Carbon::parse($session->start_time)->format('D, M j'),
                    'entries' => [],
                    'total_seconds' => 0,
                ];
            }

            $activities[$date]['total_seconds'] += $session->duration_seconds;
            $activities[$date]['entries'][] = [
                'id' => $session->id,
                'task_title' => $session->task_title ?? 'Unknown Task',
                'project_name' => $session->project_name,
                'project_color' => $session->project_color ?? '#9CA3AF',
                'start_time' => Carbon::parse($session->start_time)->format('H:i'),
                'end_time' => Carbon::parse($session->end_time)->format('H:i'),
                'duration' => $this->formatTime((int) $session->duration_seconds),
            ];
        }

        foreach ($activities as &$day) {
            $day['formatted_total'] = $this->formatTime($day['total_seconds']);
        }

        return array_slice(array_values($activities), 0, $limit);
    }

    public function formatTime(int $seconds): string
    {
        if ($seconds <= 0) {
            return '00:00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function invalidateCache(int $userId): void
    {
        foreach (['week', 'month', 'year', 'all'] as $period) {
            Cache::forget("dashboard:{$userId}:{$period}");
        }
    }

    public function getStartDate(string $period): Carbon
    {
        return match ($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            'all' => Carbon::create(2000, 1, 1),
            default => Carbon::now()->startOfYear(),
        };
    }

    private function sumSecondsFromQuery($query): int
    {
        $result = (clone $query)
            ->selectRaw('SUM(EXTRACT(EPOCH FROM (time_sessions.end_time - time_sessions.start_time)))::bigint as total')
            ->value('total');

        return (int) ($result ?? 0);
    }
}
