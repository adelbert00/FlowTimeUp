<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        $tasks = Task::where('user_id', $userId)
            ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->with(['project', 'tags'])
            ->get();

        $timeSessions = TimeSession::whereHas('task', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
            ->whereNotNull('end_time')
            ->with(['task.project', 'task.tags'])
            ->get()
            ->groupBy(function ($session) {
                return Carbon::parse($session->start_time)->format('Y-m-d');
            });

        $dailyTotals = [];
        foreach ($timeSessions as $date => $sessions) {
            $totalSeconds = $sessions->sum(function ($session) {
                return $session->start_time->diffInSeconds($session->end_time);
            });
            $dailyTotals[$date] = $this->formatDuration($totalSeconds);
        }

        return Inertia::render('Calendar/Index', [
            'month' => (int) $month,
            'year' => (int) $year,
            'tasks' => $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'due_date' => $task->due_date?->format('Y-m-d'),
                    'priority' => $task->priority,
                    'completed' => $task->completed,
                    'project' => $task->project ? [
                        'name' => $task->project->name,
                        'color' => $task->project->color ?? '#6366f1',
                    ] : null,
                    'tags' => $task->tags->map(fn($tag) => $tag->name),
                ];
            }),
            'dailyTotals' => $dailyTotals,
            'projects' => Project::where('user_id', $userId)->get(),
        ]);
    }

    private function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
