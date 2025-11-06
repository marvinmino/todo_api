<?php

namespace Database\Factories;

use App\Models\TodoList;
use App\Models\TodoListShare;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TodoListShare>
 */
class TodoListShareFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'todo_list_id' => TodoList::factory(),
            'user_id' => User::factory(),
            'permission' => fake()->randomElement(['view', 'edit', 'delete']),
        ];
    }

    /**
     * Indicate that the share has read permission.
     */
    public function readOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'permission' => 'view',
        ]);
    }

    /**
     * Indicate that the share has write permission.
     */
    public function write(): static
    {
        return $this->state(fn (array $attributes) => [
            'permission' => 'edit',
        ]);
    }
}

