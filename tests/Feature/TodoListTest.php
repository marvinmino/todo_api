<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    public function test_get_todo_lists_returns_paginated_lists(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        TodoList::factory()->count(15)->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todo-lists?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'title', 'description', 'is_favorite'],
                ],
                'pagination',
            ])
            ->assertJsonCount(10, 'data');
    }

    public function test_get_todo_lists_with_filters_favorites(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        TodoList::factory()->count(3)->favorite()->create(['user_id' => $user->id]);
        TodoList::factory()->count(2)->notFavorite()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todo-lists?is_favorite=true');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_get_todo_lists_with_search(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        TodoList::factory()->create(['user_id' => $user->id, 'title' => 'Find this list']);
        TodoList::factory()->create(['user_id' => $user->id, 'title' => 'Other list']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todo-lists?search=Find this');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_create_todo_list_creates_list(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $data = [
            'title' => 'Test List',
            'description' => 'Test Description',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todo-lists', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'title', 'description'],
            ]);

        $this->assertDatabaseHas('todo_lists', [
            'user_id' => $user->id,
            'title' => 'Test List',
        ]);
    }

    public function test_get_todo_list_by_id_returns_list(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todo-lists/{$todoList->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todoList->id,
                    'title' => $todoList->title,
                ],
            ]);
    }

    public function test_get_todo_list_by_id_unauthorized(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todo-lists/{$todoList->id}");

        $response->assertStatus(404);
    }

    public function test_update_todo_list_updates_list(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = ['title' => 'Updated Title'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/todo-lists/{$todoList->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Updated Title',
                ],
            ]);

        $this->assertDatabaseHas('todo_lists', [
            'id' => $todoList->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_delete_todo_list_deletes_list(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/todo-lists/{$todoList->id}");

        $response->assertStatus(200);
        $this->assertNull(TodoList::find($todoList->id));
    }

    public function test_archive_todo_list_archives_list(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todo-lists/{$todoList->id}/archive");

        $response->assertStatus(200);
        $this->assertSoftDeleted('todo_lists', ['id' => $todoList->id]);
    }

    public function test_restore_todo_list_restores_list(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todoList->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todo-lists/{$todoList->id}/restore");

        $response->assertStatus(200);
        $this->assertNull($todoList->fresh()->deleted_at);
    }

    public function test_toggle_favorite_toggles_favorite(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id, 'is_favorite' => false]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todo-lists/{$todoList->id}/toggle-favorite");

        $response->assertStatus(200);
        $this->assertTrue($todoList->fresh()->is_favorite);
    }
}

