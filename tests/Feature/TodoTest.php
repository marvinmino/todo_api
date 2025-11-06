<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TodoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_get_todos_returns_paginated_todos(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(15)->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todos?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'title', 'description', 'completed', 'priority'],
                ],
                'pagination',
            ])
            ->assertJsonCount(10, 'data');
    }

    public function test_get_todos_with_filters_completed(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(5)->completed()->create(['todo_list_id' => $todoList->id]);
        Todo::factory()->count(3)->incomplete()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todos?completed=true');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_get_todos_with_filters_priority(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id, 'priority' => 'high']);
        Todo::factory()->count(2)->create(['todo_list_id' => $todoList->id, 'priority' => 'low']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todos?priority=high');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_get_todos_with_search(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->create(['todo_list_id' => $todoList->id, 'title' => 'Find this todo']);
        Todo::factory()->create(['todo_list_id' => $todoList->id, 'title' => 'Other todo']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todos?search=Find this');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_create_todo_creates_todo(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = [
            'todo_list_id' => $todoList->id,
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'priority' => 'high',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'title', 'description', 'priority'],
            ]);

        $this->assertDatabaseHas('todos', [
            'todo_list_id' => $todoList->id,
            'title' => 'Test Todo',
        ]);
    }

    public function test_create_todo_with_image(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $image = UploadedFile::fake()->image('test.jpg');
        $data = [
            'todo_list_id' => $todoList->id,
            'title' => 'Test Todo',
            'image' => $image,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', $data);

        $response->assertStatus(201);
        $todo = Todo::where('title', 'Test Todo')->first();
        $this->assertNotNull($todo->image_path);
    }

    public function test_create_todo_with_tags(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $tags = Tag::factory()->count(2)->create(['user_id' => $user->id]);
        $data = [
            'todo_list_id' => $todoList->id,
            'title' => 'Test Todo',
            'tag_ids' => $tags->pluck('id')->toArray(),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', $data);

        $response->assertStatus(201);
        $todo = Todo::where('title', 'Test Todo')->first();
        $this->assertCount(2, $todo->tags);
    }

    public function test_create_todo_creates_sub_todo(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $parentTodo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $data = [
            'todo_list_id' => $todoList->id,
            'title' => 'Sub Todo',
            'parent_id' => $parentTodo->id,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', $data);

        $response->assertStatus(201);
        $subTodo = Todo::where('title', 'Sub Todo')->first();
        $this->assertEquals($parentTodo->id, $subTodo->parent_id);
    }

    public function test_get_todo_by_id_returns_todo(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todos/{$todo->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todo->id,
                    'title' => $todo->title,
                ],
            ]);
    }

    public function test_update_todo_updates_todo(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $data = ['title' => 'Updated Title'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/todos/{$todo->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Updated Title',
                ],
            ]);
    }

    public function test_delete_todo_deletes_todo(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(200);
        $this->assertNull(Todo::find($todo->id));
    }

    public function test_archive_todo_archives_todo(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todos/{$todo->id}/archive");

        $response->assertStatus(200);
        $this->assertSoftDeleted('todos', ['id' => $todo->id]);
    }

    public function test_restore_todo_restores_todo(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $todo->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todos/{$todo->id}/restore");

        $response->assertStatus(200);
        $this->assertNull($todo->fresh()->deleted_at);
    }
}

