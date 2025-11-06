<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetTodosRequest;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Http\Traits\ApiResponse;
use App\Models\Todo;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    use ApiResponse;

    /**
     * Create a new TodoController instance.
     */
    public function __construct(
        protected TodoService $todoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(GetTodosRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->get('per_page', 15);
        $todos = $this->todoService->getAllTodos(auth()->user(), $filters, $perPage);

        $collection = TodoResource::collection($todos);
        return $this->collection(
            $collection,
            'data',
            'Todos retrieved successfully',
            200,
            $todos
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Add image file to data if present
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $todo = $this->todoService->createTodo($data, auth()->user());

            return $this->resource(
                new TodoResource($todo),
                'Todo created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo): JsonResponse
    {
        $foundTodo = $this->todoService->getTodoById($todo->id, auth()->user());

        if (!$foundTodo) {
            return $this->error('Todo not found', 404);
        }
        
        if (!$this->todoService->belongsToUser($todo, auth()->user())) {
            return $this->error('Unauthorized', 403);
        }

        return $this->resource(
            new TodoResource($foundTodo),
            'Todo retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo): JsonResponse
    {
        try {
            $data = $request->validated();

            // Add image file to data if present
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $updatedTodo = $this->todoService->updateTodo($todo, $data, auth()->user());

            return $this->resource(
                new TodoResource($updatedTodo),
                'Todo updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo): JsonResponse
    {
        try {
            $this->todoService->deleteTodo($todo, auth()->user());

            return $this->success(null, 'Todo deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Archive a todo.
     */
    public function archive(Todo $todo): JsonResponse
    {
        try {
            $this->todoService->archiveTodo($todo, auth()->user());

            return $this->success(null, 'Todo archived successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Restore a todo.
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $todo = $this->todoService->restoreTodo($id, auth()->user());

            return $this->resource(
                new TodoResource($todo),
                'Todo restored successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
