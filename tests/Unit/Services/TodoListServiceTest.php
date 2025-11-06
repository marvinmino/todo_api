<?php

namespace Tests\Unit\Services;

use App\Models\TodoList;
use App\Models\User;
use App\Services\TodoListService;
use Tests\TestCase;

class TodoListServiceTest extends TestCase
{
    private TodoListService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TodoListService();
    }

    public function test_get_all_todo_lists_with_pagination(): void
    {
        $user = User::factory()->create();
        TodoList::factory()->count(20)->create(['user_id' => $user->id]);

        $result = $this->service->getAllTodoLists($user, [], 10);

        $this->assertCount(10, $result->items());
        $this->assertEquals(20, $result->total());
    }

    public function test_get_all_todo_lists_with_filters_favorites(): void
    {
        $user = User::factory()->create();
        TodoList::factory()->count(3)->favorite()->create(['user_id' => $user->id]);
        TodoList::factory()->count(2)->notFavorite()->create(['user_id' => $user->id]);

        $result = $this->service->getAllTodoLists($user, ['is_favorite' => true], 15);

        $this->assertCount(3, $result->items());
        $result->each(function ($list) {
            $this->assertTrue($list->is_favorite);
        });
    }

    public function test_get_all_todo_lists_with_search(): void
    {
        $user = User::factory()->create();
        TodoList::factory()->create(['user_id' => $user->id, 'title' => 'Find this list']);
        TodoList::factory()->create(['user_id' => $user->id, 'title' => 'Other list']);

        $result = $this->service->getAllTodoLists($user, ['search' => 'Find this'], 15);

        $this->assertCount(1, $result->items());
        $this->assertStringContainsString('Find this', $result->items()[0]->title);
    }

    public function test_create_todo_list_creates_list(): void
    {
        $user = User::factory()->create();
        $data = [
            'title' => 'Test List',
            'description' => 'Test Description',
        ];

        $result = $this->service->createTodoList($user, $data);

        $this->assertInstanceOf(TodoList::class, $result);
        $this->assertEquals('Test List', $result->title);
        $this->assertEquals($user->id, $result->user_id);
    }

    public function test_get_todo_list_by_id_returns_list(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);

        $result = $this->service->getTodoListById($todoList->id, $user);

        $this->assertInstanceOf(TodoList::class, $result);
        $this->assertEquals($todoList->id, $result->id);
    }

    public function test_update_todo_list_updates_list(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = ['title' => 'Updated Title'];

        $result = $this->service->updateTodoList($todoList, $data);

        $this->assertEquals('Updated Title', $result->title);
    }

    public function test_delete_todo_list_deletes_list(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);

        $result = $this->service->deleteTodoList($todoList);

        $this->assertTrue($result);
        $this->assertNull(TodoList::find($todoList->id));
    }

    public function test_archive_todo_list_archives_list(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);

        $result = $this->service->archiveTodoList($todoList, $user);

        $this->assertTrue($result);
        $this->assertSoftDeleted('todo_lists', ['id' => $todoList->id]);
    }

    public function test_restore_todo_list_restores_list(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todoList->delete();

        $result = $this->service->restoreTodoList($todoList->id, $user);

        $this->assertInstanceOf(TodoList::class, $result);
        $this->assertNull($result->deleted_at);
    }

    public function test_toggle_favorite_toggles_favorite_status(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id, 'is_favorite' => false]);

        $result = $this->service->toggleFavorite($todoList, $user);

        $this->assertTrue($result->is_favorite);

        $result = $this->service->toggleFavorite($todoList, $user);

        $this->assertFalse($result->is_favorite);
    }

    public function test_belongs_to_user_returns_true(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);

        $result = $this->service->belongsToUser($todoList, $user);

        $this->assertTrue($result);
    }

    public function test_belongs_to_user_returns_false(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $otherUser->id]);

        $result = $this->service->belongsToUser($todoList, $user);

        $this->assertFalse($result);
    }
}

