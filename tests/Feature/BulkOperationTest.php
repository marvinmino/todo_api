<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class BulkOperationTest extends TestCase
{
    public function test_bulk_update_updates_multiple_todos(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todos = Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id]);
        $data = [
            'todo_ids' => $todos->pluck('id')->toArray(),
            'completed' => true,
            'priority' => 'high',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos/bulk-update', $data);

        $response->assertStatus(200);
        $todos->each(function ($todo) {
            $todo->refresh();
            $this->assertTrue($todo->completed);
            $this->assertEquals('high', $todo->priority);
        });
    }

    public function test_bulk_delete_deletes_multiple_todos(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todos = Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id]);
        $data = [
            'todo_ids' => $todos->pluck('id')->toArray(),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos/bulk-delete', $data);

        $response->assertStatus(200);
        $todos->each(function ($todo) {
            $this->assertNull(Todo::find($todo->id));
        });
    }

    public function test_bulk_assign_tags_assigns_tags(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todos = Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id]);
        $tags = Tag::factory()->count(2)->create(['user_id' => $user->id]);
        $data = [
            'todo_ids' => $todos->pluck('id')->toArray(),
            'tag_ids' => $tags->pluck('id')->toArray(),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos/bulk-assign-tags', $data);

        $response->assertStatus(200);
        $todos->each(function ($todo) use ($tags) {
            $todo->refresh();
            $this->assertCount(2, $todo->tags);
        });
    }
}

