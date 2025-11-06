<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoListNoteRequest;
use App\Http\Resources\TodoListNoteResource;
use App\Http\Traits\ApiResponse;
use App\Models\TodoList;
use App\Models\TodoListNote;
use App\Services\TodoListNoteService;
use Illuminate\Http\JsonResponse;

class TodoListNoteController extends Controller
{
    use ApiResponse;

    /**
     * Create a new TodoListNoteController instance.
     */
    public function __construct(
        protected TodoListNoteService $noteService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(TodoList $todoList): JsonResponse
    {
        try {
            $notes = $this->noteService->getAllNotes($todoList, auth()->user());

            return $this->collection(
                TodoListNoteResource::collection($notes),
                'data',
                'Notes retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoListNoteRequest $request, TodoList $todoList): JsonResponse
    {
        try {
            $note = $this->noteService->createNote(
                $todoList,
                $request->validated(),
                auth()->user()
            );

            return $this->resource(
                new TodoListNoteResource($note),
                'Note created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TodoList $todoList, TodoListNote $note): JsonResponse
    {
        try {
            $foundNote = $this->noteService->getNoteById($todoList, $note->id, auth()->user());

            return $this->resource(
                new TodoListNoteResource($foundNote),
                'Note retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTodoListNoteRequest $request, TodoList $todoList, TodoListNote $note): JsonResponse
    {
        try {
            $updatedNote = $this->noteService->updateNote(
                $todoList,
                $note,
                $request->validated(),
                auth()->user()
            );

            return $this->resource(
                new TodoListNoteResource($updatedNote),
                'Note updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TodoList $todoList, TodoListNote $note): JsonResponse
    {
        try {
            $this->noteService->deleteNote($todoList, $note, auth()->user());

            return $this->success(null, 'Note deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
