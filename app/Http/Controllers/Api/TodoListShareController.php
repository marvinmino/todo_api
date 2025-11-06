<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShareTodoListRequest;
use App\Http\Requests\UpdateTodoListShareRequest;
use App\Http\Resources\TodoListShareResource;
use App\Http\Traits\ApiResponse;
use App\Models\TodoList;
use App\Models\TodoListShare;
use App\Services\TodoListShareService;
use Illuminate\Http\JsonResponse;

class TodoListShareController extends Controller
{
    use ApiResponse;

    /**
     * Create a new TodoListShareController instance.
     */
    public function __construct(
        protected TodoListShareService $shareService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(TodoList $todoList): JsonResponse
    {
        try {
            $shares = $this->shareService->getAllShares($todoList, auth()->user());

            return $this->collection(
                TodoListShareResource::collection($shares),
                'data',
                'Shares retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShareTodoListRequest $request, TodoList $todoList): JsonResponse
    {
        try {
            $share = $this->shareService->shareTodoList(
                $todoList,
                $request->user_id,
                $request->permission,
                auth()->user()
            );

            return $this->resource(
                new TodoListShareResource($share),
                'Todo list shared successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TodoList $todoList, TodoListShare $share): JsonResponse
    {
        try {
            if ($share->todo_list_id !== $todoList->id) {
                return $this->error('Share not found in this todo list', 404);
            }

            return $this->resource(
                new TodoListShareResource($share),
                'Share retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoListShareRequest $request, TodoList $todoList, TodoListShare $share): JsonResponse
    {
        try {
            if ($share->todo_list_id !== $todoList->id) {
                return $this->error('Share not found in this todo list', 404);
            }

            $updatedShare = $this->shareService->updateShare(
                $share,
                $request->permission,
                auth()->user()
            );

            return $this->resource(
                new TodoListShareResource($updatedShare),
                'Share updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TodoList $todoList, TodoListShare $share): JsonResponse
    {
        try {
            if ($share->todo_list_id !== $todoList->id) {
                return $this->error('Share not found in this todo list', 404);
            }

            $this->shareService->removeShare($share, auth()->user());

            return $this->success(null, 'Share removed successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
