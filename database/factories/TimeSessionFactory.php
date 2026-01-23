<?php

namespace Database\Factories;

use App\Models\TimeSession;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeSessionFactory extends Factory
{
    protected $model = TimeSession::class;

    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-1 week', 'now');
        $endTime = fake()->dateTimeBetween($startTime, 'now');

        return [
            'task_id' => Task::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_billable' => fake()->boolean(80),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
