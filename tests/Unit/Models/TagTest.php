<?php

namespace Tests\Unit\Models;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class TagTest extends TestCase
{
    public function test_user_relationship(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $tag->user);
        $this->assertEquals($user->id, $tag->user->id);
    }

    public function test_todos_relationship(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $tag = Tag::factory()->create(['user_id' => $user->id]);
        $todos = Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id]);
        $tag->todos()->attach($todos->pluck('id'));

        $this->assertCount(3, $tag->todos);
        $tag->todos->each(function ($todo) use ($todos) {
            $this->assertTrue($todos->contains($todo));
        });
    }
}

