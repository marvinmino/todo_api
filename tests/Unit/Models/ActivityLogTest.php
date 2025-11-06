<?php

namespace Tests\Unit\Models;

use App\Models\ActivityLog;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    public function test_user_relationship(): void
    {
        $user = User::factory()->create();
        $log = ActivityLog::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }

    public function test_loggable_polymorphic_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $log = ActivityLog::factory()->create([
            'user_id' => $user->id,
            'loggable_type' => Todo::class,
            'loggable_id' => $todo->id,
        ]);

        $this->assertInstanceOf(Todo::class, $log->loggable);
        $this->assertEquals($todo->id, $log->loggable->id);
    }
}

