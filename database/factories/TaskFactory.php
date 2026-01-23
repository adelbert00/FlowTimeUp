<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $dueDate = fake()->optional(0.5)->dateTimeBetween('now', '+1 year');
        
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'user_id' => User::factory(),
            'project_id' => null,
            'due_date' => $dueDate ? $dueDate->format('Y-m-d') : null,
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'completed' => false,
            'hourly_rate' => fake()->optional()->randomFloat(2, 10, 200),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'PLN']),
            'is_recurring' => false,
            'recurrence_type' => null,
            'recurrence_interval' => 1,
            'recurrence_ends_at' => null,
        ];
    }
}
