<?php

namespace Tests\Unit\Models;

use App\Models\TodoList;
use App\Models\TodoListShare;
use App\Models\User;
use Tests\TestCase;

class TodoListShareTest extends TestCase
{
    public function test_todo_list_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $share = TodoListShare::factory()->create(['todo_list_id' => $todoList->id]);

        $this->assertInstanceOf(TodoList::class, $share->todoList);
        $this->assertEquals($todoList->id, $share->todoList->id);
    }

    public function test_user_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create();
        $share = TodoListShare::factory()->create([
            'todo_list_id' => $todoList->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $share->user);
        $this->assertEquals($user->id, $share->user->id);
    }
}

