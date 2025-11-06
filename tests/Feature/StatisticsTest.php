<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    public function test_get_statistics_returns_statistics(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(10)->completed()->create(['todo_list_id' => $todoList->id]);
        Todo::factory()->count(5)->incomplete()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data',
            ]);
    }

    public function test_get_dashboard_returns_dashboard_stats(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(10)->completed()->create(['todo_list_id' => $todoList->id]);
        Todo::factory()->count(5)->incomplete()->create(['todo_list_id' => $todoList->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'total_todo_lists',
                    'total_todos',
                    'completed_todos',
                    'pending_todos',
                    'completion_rate',
                    'todos_by_priority',
                    'overdue_todos',
                    'due_today',
                    'due_this_week',
                    'favorite_lists',
                ],
            ]);
    }
}

