<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeSession;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Demo User
        $user = User::factory()->create([
            'first_name' => 'Demo',
            'last_name' => 'User',
            'email' => 'demo@flowtimeup.com',
            'password' => Hash::make('password123'),
        ]);

        // Create Demo Project
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Web Development',
            'color' => '#3b82f6',
        ]);

        // Create Demo Tags
        $tagBug = Tag::factory()->create(['user_id' => $user->id, 'name' => 'bugfix']);
        $tagFeat = Tag::factory()->create(['user_id' => $user->id, 'name' => 'feature']);

        // Create Demo Tasks
        $task1 = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'name' => 'Implement Reports UI',
            'billable_rate' => 50,
        ]);
        $task1->tags()->attach([$tagFeat->id]);

        $task2 = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'name' => 'Fix Migration Bug',
            'billable_rate' => 60,
        ]);
        $task2->tags()->attach([$tagBug->id]);

        // Create Demo Sessions (Past 7 days)
        for ($i = 0; $i < 5; $i++) {
            TimeSession::factory()->create([
                'user_id' => $user->id,
                'task_id' => $task1->id,
                'start_time' => Carbon::now()->subDays($i)->setTime(10, 0),
                'end_time' => Carbon::now()->subDays($i)->setTime(14, 0),
                'duration' => 14400,
                'is_billable' => true,
                'billable_rate' => 50,
            ]);

            TimeSession::factory()->create([
                'user_id' => $user->id,
                'task_id' => $task2->id,
                'start_time' => Carbon::now()->subDays($i)->setTime(15, 0),
                'end_time' => Carbon::now()->subDays($i)->setTime(16, 30),
                'duration' => 5400,
                'is_billable' => true,
                'billable_rate' => 60,
            ]);
        }
    }
}
