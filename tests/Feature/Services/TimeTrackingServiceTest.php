<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeSession;
use App\Services\TimeTrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('getDashboardStats returns correct total and breakdowns', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create(['user_id' => $user->id, 'project_id' => $project->id]);

    // Create session 2 hours ago to 1 hour ago (3600 seconds)
    TimeSession::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'start_time' => Carbon::now()->subHours(2),
        'end_time' => Carbon::now()->subHour(),
        'is_billable' => true,
    ]);

    // Create another session 30 mins ago to now (1800 seconds)
    TimeSession::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'start_time' => Carbon::now()->subMinutes(30),
        'end_time' => Carbon::now(),
        'is_billable' => true,
    ]);

    $service = new TimeTrackingService();
    $stats = $service->getDashboardStats($user->id, 'month');

    // total_seconds should be 3600 + 1800 = 5400
    expect($stats['total_seconds'])->toBe(5400);
    expect($stats['today_seconds'])->toBe(5400);
    expect($stats['weekly_seconds'])->toBe(5400);

    // project_breakdown should have 1 entry
    expect($stats['project_breakdown'])->toHaveCount(1);
    expect($stats['project_breakdown'][0]['seconds'])->toBe(5400);
    expect($stats['project_breakdown'][0]['name'])->toBe($project->name);

    // monthly_data should contain the current month with data
    $currentMonthKey = Carbon::now()->format('Y-m');
    $monthEntry = collect($stats['monthly_data'])->firstWhere('month_full', Carbon::now()->format('F'));
    expect($monthEntry['total_seconds'])->toBe(5400);
});

test('getDashboardStats respects period filters', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    // Session from last year
    TimeSession::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'start_time' => Carbon::now()->subYear()->startOfYear(),
        'end_time' => Carbon::now()->subYear()->startOfYear()->addHour(),
    ]);

    $service = new TimeTrackingService();
    
    // Check 'month' stats
    $monthStats = $service->getDashboardStats($user->id, 'month');
    expect($monthStats['total_seconds'])->toBe(0);

    // Check 'all' stats
    Cache::forget("dashboard:{$user->id}:all");
    $allStats = $service->getDashboardStats($user->id, 'all');
    expect($allStats['total_seconds'])->toBe(3600);
});
