<?php

namespace Database\Factories;

use App\Models\TodoList;
use App\Models\TodoListNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TodoListNote>
 */
class TodoListNoteFactory extends Factory
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
            'note' => fake()->paragraph(),
        ];
    }
}

