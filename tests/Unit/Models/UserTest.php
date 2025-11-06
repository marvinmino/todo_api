<?php

namespace Tests\Unit\Models;

use App\Models\ActivityLog;
use App\Models\Tag;
use App\Models\TodoList;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_todo_lists_relationship(): void
    {
        $user = User::factory()->create();
        TodoList::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->todoLists);
        $user->todoLists->each(function ($todoList) use ($user) {
            $this->assertEquals($user->id, $todoList->user_id);
        });
    }

    public function test_tags_relationship(): void
    {
        $user = User::factory()->create();
        Tag::factory()->create(['user_id' => $user->id, 'name' => 'Tag 1']);
        Tag::factory()->create(['user_id' => $user->id, 'name' => 'Tag 2']);
        Tag::factory()->create(['user_id' => $user->id, 'name' => 'Tag 3']);

        $this->assertCount(3, $user->tags);
        $user->tags->each(function ($tag) use ($user) {
            $this->assertEquals($user->id, $tag->user_id);
        });
    }

    public function test_shared_todo_lists_relationship(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $owner->id]);
        $todoList->sharedUsers()->attach($user->id, ['permission' => 'view']);

        $this->assertCount(1, $user->sharedTodoLists);
        $this->assertEquals($todoList->id, $user->sharedTodoLists->first()->id);
    }

    public function test_activity_logs_relationship(): void
    {
        $user = User::factory()->create();
        ActivityLog::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->activityLogs);
        $user->activityLogs->each(function ($log) use ($user) {
            $this->assertEquals($user->id, $log->user_id);
        });
    }
}

