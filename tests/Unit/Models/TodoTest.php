<?php

namespace Tests\Unit\Models;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoComment;
use App\Models\TodoList;
use Tests\TestCase;

class TodoTest extends TestCase
{
    public function test_todo_list_relationship(): void
    {
        $todoList = TodoList::factory()->create();
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);

        $this->assertInstanceOf(TodoList::class, $todo->todoList);
        $this->assertEquals($todoList->id, $todo->todoList->id);
    }

    public function test_parent_relationship(): void
    {
        $todoList = TodoList::factory()->create();
        $parentTodo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $subTodo = Todo::factory()->create([
            'todo_list_id' => $todoList->id,
            'parent_id' => $parentTodo->id,
        ]);

        $this->assertInstanceOf(Todo::class, $subTodo->parent);
        $this->assertEquals($parentTodo->id, $subTodo->parent->id);
    }

    public function test_sub_todos_relationship(): void
    {
        $todoList = TodoList::factory()->create();
        $parentTodo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        Todo::factory()->count(3)->create([
            'todo_list_id' => $todoList->id,
            'parent_id' => $parentTodo->id,
        ]);

        $this->assertCount(3, $parentTodo->subTodos);
        $parentTodo->subTodos->each(function ($subTodo) use ($parentTodo) {
            $this->assertEquals($parentTodo->id, $subTodo->parent_id);
        });
    }

    public function test_tags_relationship(): void
    {
        $user = \App\Models\User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $tags = Tag::factory()->count(3)->create(['user_id' => $user->id]);
        $todo->tags()->attach($tags->pluck('id'));

        $this->assertCount(3, $todo->tags);
        $todo->tags->each(function ($tag) use ($tags) {
            $this->assertTrue($tags->contains($tag));
        });
    }

    public function test_comments_relationship(): void
    {
        $todoList = TodoList::factory()->create();
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        TodoComment::factory()->count(3)->create(['todo_id' => $todo->id]);

        $this->assertCount(3, $todo->comments);
        $todo->comments->each(function ($comment) use ($todo) {
            $this->assertEquals($todo->id, $comment->todo_id);
        });
    }

    public function test_get_image_url_attribute(): void
    {
        $todo = Todo::factory()->create(['image_path' => 'todo-images/test.jpg']);

        $this->assertStringContainsString('todo-images/test.jpg', $todo->image_url);
    }

    public function test_get_image_url_attribute_returns_null_when_no_image(): void
    {
        $todo = Todo::factory()->create(['image_path' => null]);

        $this->assertNull($todo->image_url);
    }
}

