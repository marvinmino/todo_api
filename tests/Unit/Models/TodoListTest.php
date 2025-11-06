<?php

namespace Tests\Unit\Models;

use App\Models\Todo;
use App\Models\TodoList;
use App\Models\TodoListNote;
use App\Models\TodoListReminder;
use App\Models\TodoListShare;
use App\Models\User;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    public function test_user_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $todoList->user);
        $this->assertEquals($user->id, $todoList->user->id);
    }

    public function test_todos_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $this->assertCount(3, $todoList->todos);
        $todoList->todos->each(function ($todo) use ($todoList) {
            $this->assertEquals($todoList->id, $todo->todo_list_id);
        });
    }

    public function test_notes_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        TodoListNote::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $this->assertCount(3, $todoList->notes);
        $todoList->notes->each(function ($note) use ($todoList) {
            $this->assertEquals($todoList->id, $note->todo_list_id);
        });
    }

    public function test_reminders_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        TodoListReminder::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $this->assertCount(3, $todoList->reminders);
        $todoList->reminders->each(function ($reminder) use ($todoList) {
            $this->assertEquals($todoList->id, $reminder->todo_list_id);
        });
    }

    public function test_shares_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        TodoListShare::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $this->assertCount(3, $todoList->shares);
        $todoList->shares->each(function ($share) use ($todoList) {
            $this->assertEquals($todoList->id, $share->todo_list_id);
        });
    }

    public function test_shared_users_relationship(): void
    {
        $user = User::factory()->create();
        $sharedUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
            $todoList->sharedUsers()->attach($sharedUser->id, ['permission' => 'view']);

        $this->assertCount(1, $todoList->sharedUsers);
        $this->assertEquals($sharedUser->id, $todoList->sharedUsers->first()->id);
    }
}

