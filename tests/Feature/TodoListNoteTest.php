<?php

namespace Tests\Feature;

use App\Models\TodoList;
use App\Models\TodoListNote;
use App\Models\User;
use Tests\TestCase;

class TodoListNoteTest extends TestCase
{
    public function test_get_notes_returns_notes(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        TodoListNote::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todo-lists/{$todoList->id}/notes");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'note', 'created_at'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_create_note_creates_note(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = ['note' => 'Test note'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todo-lists/{$todoList->id}/notes", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'note'],
            ]);

        $this->assertDatabaseHas('todo_list_notes', [
            'todo_list_id' => $todoList->id,
            'note' => 'Test note',
        ]);
    }

    public function test_get_note_by_id_returns_note(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $note = TodoListNote::factory()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todo-lists/{$todoList->id}/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $note->id,
                    'note' => $note->note,
                ],
            ]);
    }

    public function test_update_note_updates_note(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $note = TodoListNote::factory()->create(['todo_list_id' => $todoList->id]);
        $data = ['note' => 'Updated note'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/todo-lists/{$todoList->id}/notes/{$note->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'note' => 'Updated note',
                ],
            ]);
    }

    public function test_delete_note_deletes_note(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $note = TodoListNote::factory()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/todo-lists/{$todoList->id}/notes/{$note->id}");

        $response->assertStatus(200);
        $this->assertNull(TodoListNote::find($note->id));
    }
}

