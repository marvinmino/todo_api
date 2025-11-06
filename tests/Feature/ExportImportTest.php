<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class ExportImportTest extends TestCase
{
    public function test_export_todos_exports_todos(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(5)->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/export/todos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'todos' => [
                        '*' => ['id', 'title', 'description', 'completed', 'priority'],
                    ],
                ],
            ]);
    }

    public function test_export_todo_lists_exports_lists(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        TodoList::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/export/todo-lists');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'todo_lists' => [
                        '*' => ['id', 'title', 'description'],
                    ],
                ],
            ]);
    }

    public function test_import_todos_imports_todos(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = [
            'todos' => [
                [
                    'todo_list_id' => $todoList->id,
                    'title' => 'Imported Todo 1',
                    'description' => 'Description 1',
                    'completed' => false,
                    'priority' => 'medium',
                ],
                [
                    'todo_list_id' => $todoList->id,
                    'title' => 'Imported Todo 2',
                    'description' => 'Description 2',
                    'completed' => true,
                    'priority' => 'high',
                ],
            ],
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/import/todos', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('todos', [
            'title' => 'Imported Todo 1',
        ]);
        $this->assertDatabaseHas('todos', [
            'title' => 'Imported Todo 2',
        ]);
    }

    public function test_import_todo_lists_imports_lists(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $data = [
            'todo_lists' => [
                [
                    'title' => 'Imported List 1',
                    'description' => 'Description 1',
                    'is_favorite' => false,
                ],
                [
                    'title' => 'Imported List 2',
                    'description' => 'Description 2',
                    'is_favorite' => true,
                ],
            ],
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/import/todo-lists', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('todo_lists', [
            'user_id' => $user->id,
            'title' => 'Imported List 1',
        ]);
        $this->assertDatabaseHas('todo_lists', [
            'user_id' => $user->id,
            'title' => 'Imported List 2',
        ]);
    }
}

