<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Models\TodoComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TodoComment>
 */
class TodoCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'todo_id' => Todo::factory(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'comment' => fake()->paragraph(),
        ];
    }

    /**
     * Indicate that the comment is a reply.
     */
    public function reply(TodoComment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'todo_id' => $parent->todo_id,
        ]);
    }
}

