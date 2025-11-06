<?php

namespace Database\Factories;

use App\Models\TodoList;
use App\Models\TodoListReminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TodoListReminder>
 */
class TodoListReminderFactory extends Factory
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
            'reminder_date' => fake()->dateTimeBetween('now', '+30 days'),
            'is_sent' => false,
        ];
    }

    /**
     * Indicate that the reminder has been sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sent' => true,
        ]);
    }

    /**
     * Indicate that the reminder has not been sent.
     */
    public function notSent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sent' => false,
        ]);
    }
}

