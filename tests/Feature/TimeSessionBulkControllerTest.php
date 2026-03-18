<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can bulk update sessions', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create(['user_id' => $user->id]);

    $sessions = TimeSession::factory()->count(3)->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'is_billable' => false,
    ]);

    $ids = $sessions->pluck('id')->toArray();

    $data = [
        'ids' => $ids,
        'is_billable' => true,
        'project_id' => $project->id,
    ];

    $response = $this->actingAs($user)
        ->patchJson(route('time-sessions.bulk-update'), $data);

    $response->assertOk();

    foreach ($ids as $id) {
        $this->assertDatabaseHas('time_sessions', [
            'id' => $id,
            'is_billable' => true,
        ]);
    }
});

test('it can bulk delete sessions', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $sessions = TimeSession::factory()->count(3)->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
    ]);

    $ids = $sessions->pluck('id')->toArray();

    $response = $this->actingAs($user)
        ->deleteJson(route('time-sessions.bulk-destroy'), ['ids' => $ids]);

    $response->assertOk();

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('time_sessions', ['id' => $id]);
    }
});

test('it prevents unauthorized bulk update', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $sessions = TimeSession::factory()->count(1)->create([
        'user_id' => $otherUser->id,
        'task_id' => $task->id,
    ]);

    $ids = $sessions->pluck('id')->toArray();

    $response = $this->actingAs($user)
        ->patchJson(route('time-sessions.bulk-update'), [
            'ids' => $ids,
            'is_billable' => true,
        ]);

    $response->assertStatus(403);
});
