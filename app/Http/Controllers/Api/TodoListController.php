<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetTodoListsRequest;
use App\Http\Requests\StoreTodoListRequest;
use App\Http\Requests\UpdateTodoListRequest;
use App\Http\Resources\TodoListResource;
use App\Http\Traits\ApiResponse;
use App\Models\TodoList;
use App\Services\TodoListService;
use Illuminate\Http\JsonResponse;

class TodoListController extends Controller
{
    use ApiResponse;

    /**
     * Create a new TodoListController instance.
     */
    public function __construct(
        protected TodoListService $todoListService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(GetTodoListsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->get('per_page', 15);
        $todoLists = $this->todoListService->getAllTodoLists(auth()->user(), $filters, $perPage);

        $collection = TodoListResource::collection($todoLists);
        return $this->collection(
            $collection,
            'data',
            'Todo lists retrieved successfully',
            200,
            $todoLists
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoListRequest $request): JsonResponse
    {
        $todoList = $this->todoListService->createTodoList(
            auth()->user(),
            $request->validated()
        );

        return $this->resource(
            new TodoListResource($todoList),
            'Todo list created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(TodoList $todoList): JsonResponse
    {
        $foundTodoList = $this->todoListService->getTodoListById($todoList->id, auth()->user());

        if (!$foundTodoList) {
            return $this->error('Todo list not found', 404);
        }
        
        if (!$this->todoListService->belongsToUser($todoList, auth()->user())) {
            return $this->error('Unauthorized', 403);
        }

        return $this->resource(
            new TodoListResource($foundTodoList),
            'Todo list retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoListRequest $request, TodoList $todoList): JsonResponse
    {
        if (!$this->todoListService->belongsToUser($todoList, auth()->user())) {
            return $this->error('Unauthorized', 403);
        }

        $updatedTodoList = $this->todoListService->updateTodoList(
            $todoList,
            $request->validated()
        );

        return $this->resource(
            new TodoListResource($updatedTodoList),
            'Todo list updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TodoList $todoList): JsonResponse
    {
        if (!$this->todoListService->belongsToUser($todoList, auth()->user())) {
            return $this->error('Unauthorized', 403);
        }

        $this->todoListService->deleteTodoList($todoList);

        return $this->success(null, 'Todo list deleted successfully');
    }

    /**
     * Archive a todo list.
     */
    public function archive(TodoList $todoList): JsonResponse
    {
        try {
            $this->todoListService->archiveTodoList($todoList, auth()->user());

            return $this->success(null, 'Todo list archived successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Restore a todo list.
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $todoList = $this->todoListService->restoreTodoList($id, auth()->user());

            return $this->resource(
                new TodoListResource($todoList),
                'Todo list restored successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Toggle favorite status.
     */
    public function toggleFavorite(TodoList $todoList): JsonResponse
    {
        try {
            $updatedTodoList = $this->todoListService->toggleFavorite($todoList, auth()->user());

            return $this->resource(
                new TodoListResource($updatedTodoList),
                'Favorite status updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
