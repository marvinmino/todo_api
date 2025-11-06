<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'loggable_type' => 'App\Models\Todo',
            'loggable_id' => 1,
            'action' => fake()->randomElement(['created', 'updated', 'deleted', 'archived', 'restored']),
            'description' => fake()->optional()->sentence(),
            'old_values' => null,
            'new_values' => null,
        ];
    }
}

