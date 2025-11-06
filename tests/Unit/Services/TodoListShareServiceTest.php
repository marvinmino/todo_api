<?php

namespace Tests\Unit\Services;

use App\Models\TodoList;
use App\Models\TodoListShare;
use App\Models\User;
use App\Services\TodoListShareService;
use Tests\TestCase;

class TodoListShareServiceTest extends TestCase
{
    private TodoListShareService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TodoListShareService();
    }

    public function test_get_all_shares_returns_shares(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        TodoListShare::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getAllShares($todoList, $user);

        $this->assertCount(3, $result);
    }

    public function test_get_all_shares_throws_exception_for_unauthorized(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $otherUser->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized');

        $this->service->getAllShares($todoList, $user);
    }

    public function test_share_todo_list_shares_list(): void
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $owner->id]);

        $result = $this->service->shareTodoList($todoList, $sharedUser->id, 'view', $owner);

        $this->assertInstanceOf(TodoListShare::class, $result);
        $this->assertEquals($todoList->id, $result->todo_list_id);
        $this->assertEquals($sharedUser->id, $result->user_id);
        $this->assertEquals('view', $result->permission);
    }

    public function test_share_todo_list_throws_exception_for_sharing_with_self(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot share with yourself');

        $this->service->shareTodoList($todoList, $user->id, 'view', $user);
    }

    public function test_update_share_updates_permission(): void
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $owner->id]);
        $share = TodoListShare::factory()->create([
            'todo_list_id' => $todoList->id,
            'user_id' => $sharedUser->id,
            'permission' => 'view',
        ]);

        $result = $this->service->updateShare($share, 'edit', $owner);

        $this->assertEquals('edit', $result->permission);
    }

    public function test_remove_share_removes_share(): void
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $owner->id]);
        $share = TodoListShare::factory()->create([
            'todo_list_id' => $todoList->id,
            'user_id' => $sharedUser->id,
        ]);

        $result = $this->service->removeShare($share, $owner);

        $this->assertTrue($result);
        $this->assertNull(TodoListShare::find($share->id));
    }
}

