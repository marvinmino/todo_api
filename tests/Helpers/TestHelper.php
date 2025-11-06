<?php

namespace Tests\Helpers;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

trait TestHelper
{
    use RefreshDatabase;

    /**
     * Create an authenticated user with a token.
     *
     * @param array $attributes
     * @return array{user: User, token: string}
     */
    protected function createAuthenticatedUser(array $attributes = []): array
    {
        $user = User::factory()->create($attributes);
        $token = $user->createToken('test-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Set the authenticated user for the request.
     *
     * @param User $user
     * @return $this
     */
    protected function actingAsUser(User $user): self
    {
        Sanctum::actingAs($user, ['*']);

        return $this;
    }

    /**
     * Create a todo list with todos for a user.
     *
     * @param User $user
     * @param int $todoCount
     * @param array $todoAttributes
     * @return TodoList
     */
    protected function createTodoListWithTodos(User $user, int $todoCount = 3, array $todoAttributes = []): TodoList
    {
        $todoList = TodoList::factory()->create([
            'user_id' => $user->id,
        ]);

        Todo::factory()->count($todoCount)->create(array_merge([
            'todo_list_id' => $todoList->id,
        ], $todoAttributes));

        return $todoList->fresh(['todos']);
    }

    /**
     * Create a todo with tags.
     *
     * @param User $user
     * @param TodoList $todoList
     * @param int $tagCount
     * @return Todo
     */
    protected function createTodoWithTags(User $user, TodoList $todoList, int $tagCount = 2): Todo
    {
        $todo = Todo::factory()->create([
            'todo_list_id' => $todoList->id,
        ]);

        $tags = Tag::factory()->count($tagCount)->create([
            'user_id' => $user->id,
        ]);

        $todo->tags()->attach($tags->pluck('id'));

        return $todo->fresh(['tags']);
    }

    /**
     * Create a sub-todo.
     *
     * @param Todo $parentTodo
     * @return Todo
     */
    protected function createSubTodo(Todo $parentTodo): Todo
    {
        return Todo::factory()->create([
            'todo_list_id' => $parentTodo->todo_list_id,
            'parent_id' => $parentTodo->id,
        ]);
    }
}

