<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        $today = Carbon::today();

        // Czas dzisiejszy
        $todaysSessions = TimeSession::whereHas('task', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereDate('start_time', $today)
            ->whereNotNull('end_time')
            ->get();

        $todaySeconds = $todaysSessions->sum(function ($session) {
            return $session->start_time->diffInSeconds($session->end_time);
        });

        // Zadania ukończone dzisiaj
        $completedToday = Task::where('user_id', $userId)
            ->where('completed', true)
            ->whereDate('updated_at', $today)
            ->count();

        // Aktywne projekty
        $activeProjects = Project::where('user_id', $userId)->count();

        // Pending tasks
        $pendingTasks = Task::where('user_id', $userId)
            ->where('completed', false)
            ->count();

        // Total tasks
        $totalTasks = Task::where('user_id', $userId)->count();

        // Weekly time breakdown
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        $weeklySessions = TimeSession::whereHas('task', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->whereNotNull('end_time')
            ->get();

        $weeklySeconds = $weeklySessions->sum(function ($session) {
            return $session->start_time->diffInSeconds($session->end_time);
        });

        // Recent tasks
        $recentTasks = Task::with(['project', 'timeSessions'])
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'completed' => $task->completed,
                    'project' => $task->project ? [
                        'name' => $task->project->name,
                        'color' => $task->project->color ?? '#6366f1',
                    ] : null,
                ];
            });

        return Inertia::render('Home', [
            'stats' => [
                'today_time' => $this->formatTime($todaySeconds),
                'today_seconds' => $todaySeconds,
                'completed_today' => $completedToday,
                'active_projects' => $activeProjects,
                'pending_tasks' => $pendingTasks,
                'total_tasks' => $totalTasks,
                'weekly_time' => $this->formatTime($weeklySeconds),
                'weekly_seconds' => $weeklySeconds,
            ],
            'recent_tasks' => $recentTasks,
        ]);
    }

    private function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
