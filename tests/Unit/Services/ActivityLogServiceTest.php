<?php

namespace Tests\Unit\Services;

use App\Models\ActivityLog;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use App\Services\ActivityLogService;
use Tests\TestCase;

class ActivityLogServiceTest extends TestCase
{
    private ActivityLogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ActivityLogService();
    }

    public function test_get_all_logs_with_pagination(): void
    {
        $user = User::factory()->create();
        ActivityLog::factory()->count(20)->create(['user_id' => $user->id]);

        $result = $this->service->getAllLogs($user, 10);

        $this->assertCount(10, $result->items());
        $this->assertEquals(20, $result->total());
    }

    public function test_get_log_by_id_returns_log(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $log = ActivityLog::factory()->create([
            'user_id' => $user->id,
            'loggable_type' => Todo::class,
            'loggable_id' => $todo->id,
        ]);

        $result = $this->service->getLogById($log->id, $user);

        $this->assertInstanceOf(ActivityLog::class, $result);
        $this->assertEquals($log->id, $result->id);
    }

    public function test_log_activity_creates_activity_log(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $oldValues = ['title' => 'Old Title'];
        $newValues = ['title' => 'New Title'];

        $result = $this->service->logActivity(
            $user,
            $todo,
            'updated',
            $oldValues,
            $newValues,
            'Todo was updated'
        );

        $this->assertInstanceOf(ActivityLog::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals(Todo::class, $result->loggable_type);
        $this->assertEquals($todo->id, $result->loggable_id);
        $this->assertEquals('updated', $result->action);
        $this->assertEquals('Todo was updated', $result->description);
    }
}

