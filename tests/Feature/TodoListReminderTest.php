<?php

namespace Tests\Feature;

use App\Models\TodoList;
use App\Models\TodoListReminder;
use App\Models\User;
use Tests\TestCase;

class TodoListReminderTest extends TestCase
{
    public function test_get_reminders_returns_reminders(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        TodoListReminder::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todo-lists/{$todoList->id}/reminders");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'reminder_date', 'is_sent'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_create_reminder_creates_reminder(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = [
            'reminder_date' => now()->addDay()->toDateTimeString(),
            'is_sent' => false,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todo-lists/{$todoList->id}/reminders", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'reminder_date', 'is_sent'],
            ]);

        $this->assertDatabaseHas('todo_list_reminders', [
            'todo_list_id' => $todoList->id,
        ]);
    }

    public function test_get_reminder_by_id_returns_reminder(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $reminder = TodoListReminder::factory()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todo-lists/{$todoList->id}/reminders/{$reminder->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $reminder->id,
                ],
            ]);
    }

    public function test_update_reminder_updates_reminder(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $reminder = TodoListReminder::factory()->create(['todo_list_id' => $todoList->id]);
        $data = [
            'reminder_date' => now()->addDays(2)->toDateTimeString(),
            'is_sent' => true,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/todo-lists/{$todoList->id}/reminders/{$reminder->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'is_sent' => true,
                ],
            ]);
    }

    public function test_delete_reminder_deletes_reminder(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $reminder = TodoListReminder::factory()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/todo-lists/{$todoList->id}/reminders/{$reminder->id}");

        $response->assertStatus(200);
        $this->assertNull(TodoListReminder::find($reminder->id));
    }
}

