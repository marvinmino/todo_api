<?php

namespace Tests\Unit\Services;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use App\Services\TodoService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TodoServiceTest extends TestCase
{
    private TodoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TodoService();
        Storage::fake('public');
    }

    public function test_get_all_todos_with_pagination(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(20)->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getAllTodos($user, [], 10);

        $this->assertCount(10, $result->items());
        $this->assertEquals(20, $result->total());
    }

    public function test_get_all_todos_with_filters_completed(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(5)->completed()->create(['todo_list_id' => $todoList->id]);
        Todo::factory()->count(3)->incomplete()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getAllTodos($user, ['completed' => true], 15);

        $this->assertCount(5, $result->items());
        $result->each(function ($todo) {
            $this->assertTrue($todo->completed);
        });
    }

    public function test_get_all_todos_with_filters_priority(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->count(3)->create(['todo_list_id' => $todoList->id, 'priority' => 'high']);
        Todo::factory()->count(2)->create(['todo_list_id' => $todoList->id, 'priority' => 'low']);

        $result = $this->service->getAllTodos($user, ['priority' => 'high'], 15);

        $this->assertCount(3, $result->items());
        $result->each(function ($todo) {
            $this->assertEquals('high', $todo->priority);
        });
    }

    public function test_get_all_todos_with_search(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        Todo::factory()->create(['todo_list_id' => $todoList->id, 'title' => 'Find this todo']);
        Todo::factory()->create(['todo_list_id' => $todoList->id, 'title' => 'Other todo']);

        $result = $this->service->getAllTodos($user, ['search' => 'Find this'], 15);

        $this->assertCount(1, $result->items());
        $this->assertStringContainsString('Find this', $result->items()[0]->title);
    }

    public function test_create_todo_creates_todo(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = [
            'todo_list_id' => $todoList->id,
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'priority' => 'high',
        ];

        $result = $this->service->createTodo($data, $user);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals('Test Todo', $result->title);
        $this->assertEquals($todoList->id, $result->todo_list_id);
    }

    public function test_create_todo_with_image(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $image = UploadedFile::fake()->image('test.jpg');
        $data = [
            'todo_list_id' => $todoList->id,
            'title' => 'Test Todo',
            'image' => $image,
        ];

        $result = $this->service->createTodo($data, $user);

        $this->assertNotNull($result->image_path);
        Storage::disk('public')->assertExists($result->image_path);
    }

    public function test_create_todo_with_tags(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $tags = Tag::factory()->count(2)->create(['user_id' => $user->id]);
        $data = [
            'todo_list_id' => $todoList->id,
            'title' => 'Test Todo',
            'tag_ids' => $tags->pluck('id')->toArray(),
        ];

        $result = $this->service->createTodo($data, $user);

        $this->assertCount(2, $result->tags);
    }

    public function test_create_todo_creates_sub_todo(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $parentTodo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $data = [
            'todo_list_id' => $todoList->id,
            'title' => 'Sub Todo',
            'parent_id' => $parentTodo->id,
        ];

        $result = $this->service->createTodo($data, $user);

        $this->assertEquals($parentTodo->id, $result->parent_id);
    }

    public function test_create_todo_throws_exception_for_unauthorized_todo_list(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $otherUser->id]);
        $data = [
            'todo_list_id' => $todoList->id,
            'title' => 'Test Todo',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Todo list not found or unauthorized');

        $this->service->createTodo($data, $user);
    }

    public function test_get_todo_by_id_returns_todo(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getTodoById($todo->id, $user);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals($todo->id, $result->id);
    }

    public function test_update_todo_updates_todo(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $data = ['title' => 'Updated Title'];

        $result = $this->service->updateTodo($todo, $data, $user);

        $this->assertEquals('Updated Title', $result->title);
    }

    public function test_update_todo_with_image(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create([
            'todo_list_id' => $todoList->id,
            'image_path' => 'old-image.jpg',
        ]);
        $newImage = UploadedFile::fake()->image('new.jpg');
        $data = ['image' => $newImage];

        $result = $this->service->updateTodo($todo, $data, $user);

        $this->assertNotNull($result->image_path);
        Storage::disk('public')->assertExists($result->image_path);
    }

    public function test_delete_todo_deletes_todo_and_image(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create([
            'todo_list_id' => $todoList->id,
            'image_path' => 'todo-images/test.jpg',
        ]);
        Storage::disk('public')->put($todo->image_path, 'fake content');

        $result = $this->service->deleteTodo($todo, $user);

        $this->assertTrue($result);
        $this->assertNull(Todo::find($todo->id));
    }

    public function test_archive_todo_archives_todo(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->archiveTodo($todo, $user);

        $this->assertTrue($result);
        $this->assertSoftDeleted('todos', ['id' => $todo->id]);
    }

    public function test_restore_todo_restores_todo(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);
        $todo->delete();

        $result = $this->service->restoreTodo($todo->id, $user);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertNull($result->deleted_at);
    }

    public function test_belongs_to_user_returns_true(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->belongsToUser($todo, $user);

        $this->assertTrue($result);
    }

    public function test_belongs_to_user_returns_false(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $otherUser->id]);
        $todo = Todo::factory()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->belongsToUser($todo, $user);

        $this->assertFalse($result);
    }
}

