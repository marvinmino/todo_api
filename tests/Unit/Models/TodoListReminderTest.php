<?php

namespace Tests\Unit\Models;

use App\Models\TodoList;
use App\Models\TodoListReminder;
use Tests\TestCase;

class TodoListReminderTest extends TestCase
{
    public function test_todo_list_relationship(): void
    {
        $todoList = TodoList::factory()->create();
        $reminder = TodoListReminder::factory()->create(['todo_list_id' => $todoList->id]);

        $this->assertInstanceOf(TodoList::class, $reminder->todoList);
        $this->assertEquals($todoList->id, $reminder->todoList->id);
    }
}

