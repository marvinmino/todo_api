<?php

namespace Tests\Unit\Models;

use App\Models\TodoList;
use App\Models\TodoListNote;
use Tests\TestCase;

class TodoListNoteTest extends TestCase
{
    public function test_todo_list_relationship(): void
    {
        $todoList = TodoList::factory()->create();
        $note = TodoListNote::factory()->create(['todo_list_id' => $todoList->id]);

        $this->assertInstanceOf(TodoList::class, $note->todoList);
        $this->assertEquals($todoList->id, $note->todoList->id);
    }
}

