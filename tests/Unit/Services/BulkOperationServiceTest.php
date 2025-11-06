<?php

namespace Tests\Unit\Services;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use App\Services\BulkOperationService;
use Tests\TestCase;

class BulkOperationServiceTest extends TestCase
{
    private BulkOperationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BulkOperationService();
    }

    public function test_bulk_update_updates_multiple_todos(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todos = Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id]);
        $todoIds = $todos->pluck('id')->toArray();
        $data = ['completed' => true, 'priority' => 'high'];

        $result = $this->service->bulkUpdate($todoIds, $data, $user);

        $this->assertEquals(3, $result);
        $todos->each(function ($todo) {
            $todo->refresh();
            $this->assertTrue($todo->completed);
            $this->assertEquals('high', $todo->priority);
        });
    }

    public function test_bulk_update_throws_exception_for_unauthorized_todos(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $otherUser->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $data = ['completed' => true];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Some todos not found or unauthorized');

        $this->service->bulkUpdate([$todo->id], $data, $user);
    }

    public function test_bulk_delete_deletes_multiple_todos(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todos = Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id]);
        $todoIds = $todos->pluck('id')->toArray();

        $result = $this->service->bulkDelete($todoIds, $user);

        $this->assertEquals(3, $result);
        $todos->each(function ($todo) {
            $this->assertNull(Todo::find($todo->id));
        });
    }

    public function test_bulk_delete_throws_exception_for_unauthorized_todos(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $otherUser->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Some todos not found or unauthorized');

        $this->service->bulkDelete([$todo->id], $user);
    }

    public function test_bulk_assign_tags_assigns_tags_to_todos(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todos = Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id]);
        $tags = Tag::factory()->count(2)->create(['user_id' => $user->id]);
        $todoIds = $todos->pluck('id')->toArray();
        $tagIds = $tags->pluck('id')->toArray();

        $this->service->bulkAssignTags($todoIds, $tagIds, $user);

        $todos->each(function ($todo) use ($tags) {
            $todo->refresh();
            $this->assertCount(2, $todo->tags);
            $this->assertTrue($todo->tags->contains($tags->first()));
        });
    }

    public function test_bulk_assign_tags_throws_exception_for_unauthorized_todos(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $otherUser->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $tags = Tag::factory()->count(1)->create(['user_id' => $user->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Some todos not found or unauthorized');

        $this->service->bulkAssignTags([$todo->id], $tags->pluck('id')->toArray(), $user);
    }
}

