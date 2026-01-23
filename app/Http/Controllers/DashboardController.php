<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        $user = $request->user();
        $today = Carbon::today();
        
        $period = $request->get('period', 'year');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();

        $totalSeconds = $this->calculateTotalSeconds($userId, $startDate, $endDate);

        $monthlyData = $this->getMonthlyBreakdown($userId, $startDate, $endDate);
        $projectBreakdown = $this->getProjectBreakdown($userId, $startDate, $endDate);
        $topProject = !empty($projectBreakdown) ? $projectBreakdown[0] : null;

        $todaySeconds = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->where('tasks.user_id', $userId)
            ->whereDate('time_sessions.start_time', $today)
            ->whereNotNull('time_sessions.end_time')
            ->get()
            ->sum(fn($session) => $session->start_time->diffInSeconds($session->end_time));

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        $weeklySeconds = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->where('tasks.user_id', $userId)
            ->whereBetween('time_sessions.start_time', [$weekStart, $weekEnd])
            ->whereNotNull('time_sessions.end_time')
            ->get()
            ->sum(fn($session) => $session->start_time->diffInSeconds($session->end_time));

        $completedToday = Task::where('user_id', $userId)
            ->where('completed', true)
            ->whereDate('updated_at', $today)
            ->count();

        $activeProjects = Project::where('user_id', $userId)->count();

        $pendingTasks = Task::where('user_id', $userId)
            ->where('completed', false)
            ->count();

        $totalTasks = Task::where('user_id', $userId)->count();

        $teamActivities = $this->getTeamActivities($userId, 10);

        $monthlyData = $monthlyData ?? [];
        $projectBreakdown = $projectBreakdown ?? [];
        $teamActivities = $teamActivities ?? [];

        return Inertia::render('Home', [
            'stats' => [
                'total_time' => $this->formatTime($totalSeconds),
                'total_seconds' => $totalSeconds,
                'today_time' => $this->formatTime($todaySeconds),
                'today_seconds' => $todaySeconds,
                'weekly_time' => $this->formatTime($weeklySeconds),
                'weekly_seconds' => $weeklySeconds,
                'completed_today' => $completedToday,
                'active_projects' => $activeProjects,
                'pending_tasks' => $pendingTasks,
                'total_tasks' => $totalTasks,
            ],
            'top_project' => $topProject ? [
                'name' => $topProject['name'],
                'time' => $topProject['formatted_time'],
            ] : null,
            'monthly_data' => $monthlyData,
            'project_breakdown' => $projectBreakdown,
            'team_activities' => $teamActivities,
            'period' => $period,
            'user' => [
                'name' => $user->name ?? 'User',
                'first_name' => $user->first_name ?? (!empty($user->name) ? explode(' ', $user->name)[0] : 'User'),
            ],
        ]);
    }

    private function getStartDate(string $period): Carbon
    {
        return match ($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            'all' => Carbon::create(2000, 1, 1),
            default => Carbon::now()->startOfYear(),
        };
    }

    private function getMonthlyBreakdown(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $months = [];
        $current = $startDate->copy()->startOfMonth();
        
        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            $sessions = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
                ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
                ->where('tasks.user_id', $userId)
                ->whereBetween('time_sessions.start_time', [$monthStart, $monthEnd])
                ->whereNotNull('time_sessions.end_time')
                ->select('time_sessions.*', 'tasks.id as task_id', 'tasks.title as task_title', 'tasks.project_id', 'projects.name as project_name', 'projects.color as project_color')
                ->get();

            $totalSeconds = $sessions->sum(fn($session) => $session->start_time->diffInSeconds($session->end_time));

            $projectBreakdown = [];
            foreach ($sessions as $session) {
                $projectId = $session->project_id ?? 0;
                $projectName = $session->project_name ?? 'Without project';
                $projectColor = $session->project_color ?? '#9CA3AF';
                
                if (!isset($projectBreakdown[$projectId])) {
                    $projectBreakdown[$projectId] = [
                        'id' => $projectId,
                        'name' => $projectName,
                        'color' => $projectColor,
                        'seconds' => 0,
                    ];
                }
                $projectBreakdown[$projectId]['seconds'] += $session->start_time->diffInSeconds($session->end_time);
            }

            $months[] = [
                'month' => $current->format('M'),
                'month_full' => $current->format('F'),
                'year' => $current->format('Y'),
                'total_seconds' => $totalSeconds,
                'formatted_time' => $this->formatTime($totalSeconds),
                'projects' => array_values($projectBreakdown),
            ];
            
            $current->addMonth();
        }
        
        return $months;
    }

    private function getProjectBreakdown(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $sessions = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('tasks.user_id', $userId)
            ->where('time_sessions.start_time', '>=', $startDate)
            ->where('time_sessions.start_time', '<=', $endDate)
            ->whereNotNull('time_sessions.end_time')
            ->select('time_sessions.*', 'tasks.id as task_id', 'tasks.title as task_title', 'tasks.project_id', 'projects.name as project_name', 'projects.color as project_color')
            ->get();

        $projectTotals = [];
        $totalSeconds = 0;

        foreach ($sessions as $session) {
            $projectId = $session->project_id ?? 0;
            $projectName = $session->project_name ?? 'Without project';
            $projectColor = $session->project_color ?? '#9CA3AF';
            
            $sessionSeconds = $session->start_time->diffInSeconds($session->end_time);
            $totalSeconds += $sessionSeconds;
            
            if (!isset($projectTotals[$projectId])) {
                $projectTotals[$projectId] = [
                    'id' => $projectId,
                    'name' => $projectName,
                    'color' => $projectColor,
                    'seconds' => 0,
                ];
            }
            $projectTotals[$projectId]['seconds'] += $sessionSeconds;
        }

        $breakdown = array_values($projectTotals);
        usort($breakdown, fn($a, $b) => $b['seconds'] - $a['seconds']);

        foreach ($breakdown as &$project) {
            $project['formatted_time'] = $this->formatTime($project['seconds']);
            $project['percentage'] = $totalSeconds > 0 
                ? round(($project['seconds'] / $totalSeconds) * 100, 2) 
                : 0;
            $project['percentage'] = max(0, min(100, $project['percentage']));
        }

        return $breakdown;
    }

    private function getTeamActivities(int $userId, int $limit = 10): array
    {
        $sessions = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('tasks.user_id', $userId)
            ->whereNotNull('time_sessions.end_time')
            ->select('time_sessions.*', 'tasks.id as task_id', 'tasks.title as task_title', 'tasks.project_id', 'projects.name as project_name', 'projects.color as project_color')
            ->orderBy('time_sessions.start_time', 'desc')
            ->take(50)
            ->get();

        $activities = [];
        
        foreach ($sessions as $session) {
            $date = $session->start_time->format('Y-m-d');
            
            if (!isset($activities[$date])) {
                $activities[$date] = [
                    'date' => $date,
                    'formatted_date' => $session->start_time->format('D, M j'),
                    'entries' => [],
                    'total_seconds' => 0,
                ];
            }
            
            $sessionSeconds = $session->start_time->diffInSeconds($session->end_time);
            $activities[$date]['total_seconds'] += $sessionSeconds;
            
            $activities[$date]['entries'][] = [
                'id' => $session->id,
                'task_title' => $session->task_title ?? 'Unknown Task',
                'project_name' => $session->project_name,
                'project_color' => $session->project_color ?? '#9CA3AF',
                'start_time' => $session->start_time->format('H:i'),
                'end_time' => $session->end_time->format('H:i'),
                'duration' => $this->formatTime($sessionSeconds),
            ];
        }

        foreach ($activities as &$day) {
            $day['formatted_total'] = $this->formatTime($day['total_seconds']);
        }

        $result = array_slice(array_values($activities), 0, $limit);
        return $result ?: [];
    }

    private function calculateTotalSeconds(int $userId, Carbon $startDate, ?Carbon $endDate = null): int
    {
        $query = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->where('tasks.user_id', $userId)
            ->where('time_sessions.start_time', '>=', $startDate)
            ->whereNotNull('time_sessions.end_time');
        
        if ($endDate) {
            $query->where('time_sessions.start_time', '<=', $endDate);
        }
        
        return $query->get()->sum(fn($session) => $session->start_time->diffInSeconds($session->end_time));
    }

    private function formatTime(int $seconds): string
    {
        if ($seconds <= 0) {
            return '00:00:00';
        }
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
