<?php

use App\Models\User;
use App\Models\Task;
use App\Models\TimeSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can store a new time session', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $data = [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'start_time' => now()->subHour()->toDateTimeString(),
        'end_time' => now()->toDateTimeString(),
        'is_billable' => true,
        'billable_rate' => 50,
        'description' => 'Test session description'
    ];

    $response = $this->actingAs($user)
        ->post(route('time-sessions.store'), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('time_sessions', [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'description' => 'Test session description'
    ]);
});

test('it prevents storing a session for another user task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

    $data = [
        'task_id' => $otherTask->id,
        'user_id' => $user->id,
        'start_time' => now()->toDateTimeString(),
    ];

    $response = $this->actingAs($user)
        ->post(route('time-sessions.store'), $data);

    // Assuming validation or policy prevents this. 
    // Let's check StoreTimeSessionRequest if possible, or just expect failure.
    $response->assertSessionHasErrors('task_id');
});
