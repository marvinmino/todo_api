<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\TodoComment;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class TodoCommentTest extends TestCase
{
    public function test_get_comments_returns_comments(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        TodoComment::factory()->count(3)->create(['todo_id' => $todo->id, 'parent_id' => null]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todos/{$todo->id}/comments");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'comment', 'user_id'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_create_comment_creates_comment(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $data = ['comment' => 'Test comment'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todos/{$todo->id}/comments", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'comment', 'user_id'],
            ]);

        $this->assertDatabaseHas('todo_comments', [
            'todo_id' => $todo->id,
            'user_id' => $user->id,
            'comment' => 'Test comment',
        ]);
    }

    public function test_create_comment_creates_reply(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $parentComment = TodoComment::factory()->create(['todo_id' => $todo->id]);
        $data = [
            'comment' => 'Reply comment',
            'parent_id' => $parentComment->id,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/todos/{$todo->id}/comments", $data);

        $response->assertStatus(201);
        $reply = TodoComment::where('comment', 'Reply comment')->first();
        $this->assertEquals($parentComment->id, $reply->parent_id);
    }

    public function test_get_comment_by_id_returns_comment(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $comment = TodoComment::factory()->create(['todo_id' => $todo->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/todos/{$todo->id}/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                ],
            ]);
    }

    public function test_update_comment_updates_comment(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $comment = TodoComment::factory()->create(['todo_id' => $todo->id, 'user_id' => $user->id]);
        $data = ['comment' => 'Updated comment'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/todos/{$todo->id}/comments/{$comment->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'comment' => 'Updated comment',
                ],
            ]);
    }

    public function test_delete_comment_deletes_comment(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $comment = TodoComment::factory()->create(['todo_id' => $todo->id, 'user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/todos/{$todo->id}/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertNull(TodoComment::find($comment->id));
    }
}

