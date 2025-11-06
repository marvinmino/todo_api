<?php

namespace Tests\Unit\Services;

use App\Models\Todo;
use App\Models\TodoComment;
use App\Models\TodoList;
use App\Models\User;
use App\Services\TodoCommentService;
use Tests\TestCase;

class TodoCommentServiceTest extends TestCase
{
    private TodoCommentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TodoCommentService();
    }

    public function test_get_all_comments_returns_comments(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        TodoComment::factory()->count(3)->create(['todo_id' => $todo->id, 'parent_id' => null]);

        $result = $this->service->getAllComments($todo, $user);

        $this->assertCount(3, $result);
    }

    public function test_create_comment_creates_comment(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $data = ['comment' => 'Test comment'];

        $result = $this->service->createComment($todo, $data, $user);

        $this->assertInstanceOf(TodoComment::class, $result);
        $this->assertEquals($todo->id, $result->todo_id);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('Test comment', $result->comment);
    }

    public function test_create_comment_creates_reply(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $parentComment = TodoComment::factory()->create(['todo_id' => $todo->id]);
        $data = ['comment' => 'Reply comment', 'parent_id' => $parentComment->id];

        $result = $this->service->createComment($todo, $data, $user);

        $this->assertEquals($parentComment->id, $result->parent_id);
    }

    public function test_get_comment_by_id_returns_comment(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $comment = TodoComment::factory()->create(['todo_id' => $todo->id]);

        $result = $this->service->getCommentById($comment->id, $user);

        $this->assertInstanceOf(TodoComment::class, $result);
        $this->assertEquals($comment->id, $result->id);
    }

    public function test_update_comment_updates_comment(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $comment = TodoComment::factory()->create(['todo_id' => $todo->id, 'user_id' => $user->id]);
        $data = ['comment' => 'Updated comment'];

        $result = $this->service->updateComment($comment, $data, $user);

        $this->assertEquals('Updated comment', $result->comment);
    }

    public function test_delete_comment_deletes_comment(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $comment = TodoComment::factory()->create(['todo_id' => $todo->id, 'user_id' => $user->id]);

        $result = $this->service->deleteComment($comment, $user);

        $this->assertTrue($result);
        $this->assertNull(TodoComment::find($comment->id));
    }
}

