<?php

namespace Tests\Unit\Services;

use App\Models\TodoList;
use App\Models\TodoListReminder;
use App\Models\User;
use App\Services\TodoListReminderService;
use Tests\TestCase;

class TodoListReminderServiceTest extends TestCase
{
    private TodoListReminderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TodoListReminderService();
    }

    public function test_get_all_reminders_returns_reminders(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        TodoListReminder::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getAllReminders($todoList, $user);

        $this->assertCount(3, $result);
    }

    public function test_create_reminder_creates_reminder(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = [
            'reminder_date' => now()->addDay(),
            'is_sent' => false,
        ];

        $result = $this->service->createReminder($todoList, $data, $user);

        $this->assertInstanceOf(TodoListReminder::class, $result);
        $this->assertEquals($todoList->id, $result->todo_list_id);
    }

    public function test_get_reminder_by_id_returns_reminder(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $reminder = TodoListReminder::factory()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getReminderById($todoList, $reminder->id, $user);

        $this->assertInstanceOf(TodoListReminder::class, $result);
        $this->assertEquals($reminder->id, $result->id);
    }

    public function test_update_reminder_updates_reminder(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $reminder = TodoListReminder::factory()->create(['todo_list_id' => $todoList->id]);
        $data = [
            'reminder_date' => now()->addDays(2),
            'is_sent' => true,
        ];

        $result = $this->service->updateReminder($todoList, $reminder, $data, $user);

        $this->assertTrue($result->is_sent);
    }

    public function test_delete_reminder_deletes_reminder(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $reminder = TodoListReminder::factory()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->deleteReminder($todoList, $reminder, $user);

        $this->assertTrue($result);
        $this->assertNull(TodoListReminder::find($reminder->id));
    }
}

