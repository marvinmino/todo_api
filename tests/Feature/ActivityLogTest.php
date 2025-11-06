<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    public function test_get_activity_logs_returns_logs_with_pagination(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        ActivityLog::factory()->count(15)->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'action', 'description'],
                ],
                'pagination',
            ])
            ->assertJsonCount(10, 'data');
    }

    public function test_get_activity_log_by_id_returns_log(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $log = ActivityLog::factory()->create([
            'user_id' => $user->id,
            'loggable_type' => Todo::class,
            'loggable_id' => $todo->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/activity-logs/{$log->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $log->id,
                    'action' => $log->action,
                ],
            ]);
    }
}

