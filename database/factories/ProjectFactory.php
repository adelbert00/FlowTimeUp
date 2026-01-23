<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'color' => fake()->hexColor(),
            'user_id' => User::factory(),
            'hourly_rate' => fake()->optional()->randomFloat(2, 20, 150),
            'budget' => fake()->optional()->randomFloat(2, 1000, 50000),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'PLN']),
            'access' => fake()->randomElement(['public', 'private']),
        ];
    }
}
