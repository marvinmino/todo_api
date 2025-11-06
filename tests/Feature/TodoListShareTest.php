<?php

namespace Tests\Feature;

use App\Models\TodoList;
use App\Models\TodoListShare;
use App\Models\User;
use Tests\TestCase;

class TodoListShareTest extends TestCase
{
    public function test_get_shares_returns_shares(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        TodoListShare::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todo-lists/{$todoList->id}/shares");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'user_id', 'permission'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_share_todo_list_shares_list(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $sharedUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = [
            'user_id' => $sharedUser->id,
            'permission' => 'view',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todo-lists/{$todoList->id}/shares", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'user_id', 'permission'],
            ]);

        $this->assertDatabaseHas('todo_list_shares', [
            'todo_list_id' => $todoList->id,
            'user_id' => $sharedUser->id,
            'permission' => 'view',
        ]);
    }

    public function test_get_share_by_id_returns_share(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $share = TodoListShare::factory()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todo-lists/{$todoList->id}/shares/{$share->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $share->id,
                ],
            ]);
    }

    public function test_update_share_updates_permission(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $share = TodoListShare::factory()->create([
            'todo_list_id' => $todoList->id,
            'permission' => 'view',
        ]);
        $data = ['permission' => 'edit'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/todo-lists/{$todoList->id}/shares/{$share->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'permission' => 'edit',
                ],
            ]);
    }

    public function test_remove_share_removes_share(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $share = TodoListShare::factory()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/todo-lists/{$todoList->id}/shares/{$share->id}");

        $response->assertStatus(200);
        $this->assertNull(TodoListShare::find($share->id));
    }
}

