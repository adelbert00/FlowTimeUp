<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\TimeTrackingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly TimeTrackingService $timeTracking) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $userId = $user->id;
        $today = Carbon::today();

        $period = $request->get('period', 'year');

        $stats = $this->timeTracking->getDashboardStats($userId, $period);

        $completedToday = Task::where('user_id', $userId)
            ->where('completed', true)
            ->whereDate('updated_at', $today)
            ->count();

        $activeProjects = Project::where('user_id', $userId)->count();

        $pendingTasks = Task::where('user_id', $userId)
            ->where('completed', false)
            ->count();

        $totalTasks = Task::where('user_id', $userId)->count();

        $teamActivities = $this->timeTracking->getRecentActivities($userId, 10);

        $projectBreakdown = $stats['project_breakdown'] ?? [];
        $topProject = !empty($projectBreakdown) ? $projectBreakdown[0] : null;

        return Inertia::render('Home', [
            'stats' => [
                'total_time' => $this->timeTracking->formatTime($stats['total_seconds']),
                'total_seconds' => $stats['total_seconds'],
                'today_time' => $this->timeTracking->formatTime($stats['today_seconds']),
                'today_seconds' => $stats['today_seconds'],
                'weekly_time' => $this->timeTracking->formatTime($stats['weekly_seconds']),
                'weekly_seconds' => $stats['weekly_seconds'],
                'completed_today' => $completedToday,
                'active_projects' => $activeProjects,
                'pending_tasks' => $pendingTasks,
                'total_tasks' => $totalTasks,
            ],
            'top_project' => $topProject ? [
                'name' => $topProject['name'],
                'time' => $topProject['formatted_time'],
            ] : null,
            'monthly_data' => $stats['monthly_data'] ?? [],
            'project_breakdown' => $projectBreakdown,
            'team_activities' => $teamActivities ?? [],
            'period' => $period,
            'user' => [
                'name' => $user->name ?? 'User',
                'first_name' => $user->first_name ?? (!empty($user->name) ? explode(' ', $user->name)[0] : 'User'),
            ],
        ]);
    }
}
