<?php

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TimeSession;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(RefreshDatabase::class);

test('it calculates project summary correctly', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create(['project_id' => $project->id, 'user_id' => $user->id]);
    
    TimeSession::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'start_time' => Carbon::now()->subHours(2),
        'end_time' => Carbon::now()->subHour(),
        'is_billable' => true,
        'billable_rate' => 100
    ]);

    $service = new ReportService();
    $summary = $service->getSummary($user->id);

    expect($summary['by_project'])->toHaveCount(1);
    expect((int)$summary['by_project'][0]['duration'])->toBe(3600);
    expect((float)$summary['by_project'][0]['earnings'])->toBe(100.0);
});

test('it calculates tag summary correctly', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create(['user_id' => $user->id]);
    $task->tags()->attach($tag);
    
    TimeSession::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'start_time' => Carbon::now()->subHours(1),
        'end_time' => Carbon::now(),
    ]);

    $service = new ReportService();
    $summary = $service->getSummary($user->id);

    expect($summary['by_tag'])->toHaveCount(1);
    expect((int)$summary['by_tag'][0]['duration'])->toBe(3600);
});

test('it filters by date range inclusively', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    
    // Within range
    TimeSession::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'start_time' => '2026-03-18 10:00:00',
        'end_time' => '2026-03-18 11:00:00',
    ]);

    // Outside range (next day)
    TimeSession::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'start_time' => '2026-03-19 10:00:00',
        'end_time' => '2026-03-19 11:00:00',
    ]);

    $service = new ReportService();
    $summary = $service->getSummary($user->id, '2026-03-18', '2026-03-18');

    expect((int)$summary['by_project'][0]['duration'])->toBe(3600);
});
