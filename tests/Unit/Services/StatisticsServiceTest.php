<?php

namespace Tests\Unit\Services;

use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use App\Services\StatisticsService;
use Tests\TestCase;

class StatisticsServiceTest extends TestCase
{
    private StatisticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StatisticsService();
    }

    public function test_get_dashboard_stats_returns_all_statistics(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id, 'is_favorite' => true]);
        Todo::factory()->count(10)->completed()->create(['todo_list_id' => $todoList->id]);
        Todo::factory()->count(5)->incomplete()->create(['todo_list_id' => $todoList->id]);
        Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id, 'priority' => 'high']);

        $result = $this->service->getDashboardStats($user);

        $this->assertArrayHasKey('total_todo_lists', $result);
        $this->assertArrayHasKey('total_todos', $result);
        $this->assertArrayHasKey('completed_todos', $result);
        $this->assertArrayHasKey('pending_todos', $result);
        $this->assertArrayHasKey('completion_rate', $result);
        $this->assertArrayHasKey('todos_by_priority', $result);
        $this->assertArrayHasKey('overdue_todos', $result);
        $this->assertArrayHasKey('due_today', $result);
        $this->assertArrayHasKey('due_this_week', $result);
        $this->assertArrayHasKey('favorite_lists', $result);
    }

    public function test_statistics_accuracy_completion_rate(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(8)->completed()->create(['todo_list_id' => $todoList->id]);
        Todo::factory()->count(2)->incomplete()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getDashboardStats($user);

        $this->assertEquals(10, $result['total_todos']);
        $this->assertEquals(8, $result['completed_todos']);
        $this->assertEquals(2, $result['pending_todos']);
        $this->assertEquals(80.0, $result['completion_rate']);
    }

    public function test_statistics_accuracy_priority_distribution(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(2)->create(['todo_list_id' => $todoList->id, 'priority' => 'low']);
        Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id, 'priority' => 'medium']);
        Todo::factory()->count(1)->create(['todo_list_id' => $todoList->id, 'priority' => 'high']);

        $result = $this->service->getDashboardStats($user);

        $this->assertEquals(2, $result['todos_by_priority']['low']);
        $this->assertEquals(3, $result['todos_by_priority']['medium']);
        $this->assertEquals(1, $result['todos_by_priority']['high']);
    }

    public function test_statistics_accuracy_overdue_count(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(2)->overdue()->incomplete()->create(['todo_list_id' => $todoList->id]);
        Todo::factory()->count(1)->overdue()->completed()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getDashboardStats($user);

        $this->assertEquals(2, $result['overdue_todos']);
    }
}

