<?php

namespace Tests\Unit\Services;

use App\Models\TodoList;
use App\Models\TodoListNote;
use App\Models\User;
use App\Services\TodoListNoteService;
use Tests\TestCase;

class TodoListNoteServiceTest extends TestCase
{
    private TodoListNoteService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TodoListNoteService();
    }

    public function test_get_all_notes_returns_notes(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        TodoListNote::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getAllNotes($todoList, $user);

        $this->assertCount(3, $result);
    }

    public function test_get_all_notes_throws_exception_for_unauthorized(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $otherUser->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized');

        $this->service->getAllNotes($todoList, $user);
    }

    public function test_create_note_creates_note(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $data = ['note' => 'Test note'];

        $result = $this->service->createNote($todoList, $data, $user);

        $this->assertInstanceOf(TodoListNote::class, $result);
        $this->assertEquals($todoList->id, $result->todo_list_id);
        $this->assertEquals('Test note', $result->note);
    }

    public function test_get_note_by_id_returns_note(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $note = TodoListNote::factory()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->getNoteById($todoList, $note->id, $user);

        $this->assertInstanceOf(TodoListNote::class, $result);
        $this->assertEquals($note->id, $result->id);
    }

    public function test_update_note_updates_note(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $note = TodoListNote::factory()->create(['todo_list_id' => $todoList->id]);
        $data = ['note' => 'Updated note'];

        $result = $this->service->updateNote($todoList, $note, $data, $user);

        $this->assertEquals('Updated note', $result->note);
    }

    public function test_delete_note_deletes_note(): void
    {
        $user = User::factory()->create();
        $todoList = TodoList::factory()->create(['user_id' => $user->id]);
        $note = TodoListNote::factory()->create(['todo_list_id' => $todoList->id]);

        $result = $this->service->deleteNote($todoList, $note, $user);

        $this->assertTrue($result);
        $this->assertNull(TodoListNote::find($note->id));
    }
}

