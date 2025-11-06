<?php

namespace Tests\Unit\Models;

use App\Models\Todo;
use App\Models\TodoComment;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class TodoCommentTest extends TestCase
{
    public function test_todo_relationship(): void
    {
        $todoList = TodoList::factory()->create();
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $comment = TodoComment::factory()->create(['todo_id' => $todo->id]);

        $this->assertInstanceOf(Todo::class, $comment->todo);
        $this->assertEquals($todo->id, $comment->todo->id);
    }

    public function test_user_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create();
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $comment = TodoComment::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $comment->user);
        $this->assertEquals($user->id, $comment->user->id);
    }

    public function test_parent_relationship(): void
    {
        $todoList = TodoList::factory()->create();
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $parentComment = TodoComment::factory()->create(['todo_id' => $todo->id]);
        $reply = TodoComment::factory()->create([
            'todo_id' => $todo->id,
            'parent_id' => $parentComment->id,
        ]);

        $this->assertInstanceOf(TodoComment::class, $reply->parent);
        $this->assertEquals($parentComment->id, $reply->parent->id);
    }

    public function test_replies_relationship(): void
    {
        $todoList = TodoList::factory()->create();
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $parentComment = TodoComment::factory()->create(['todo_id' => $todo->id]);
        TodoComment::factory()->count(3)->create([
            'todo_id' => $todo->id,
            'parent_id' => $parentComment->id,
        ]);

        $this->assertCount(3, $parentComment->replies);
        $parentComment->replies->each(function ($reply) use ($parentComment) {
            $this->assertEquals($parentComment->id, $reply->parent_id);
        });
    }
}

