<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoCommentRequest;
use App\Http\Resources\TodoCommentResource;
use App\Http\Traits\ApiResponse;
use App\Models\Todo;
use App\Models\TodoComment;
use App\Services\TodoCommentService;
use Illuminate\Http\JsonResponse;

class TodoCommentController extends Controller
{
    use ApiResponse;

    /**
     * Create a new TodoCommentController instance.
     */
    public function __construct(
        protected TodoCommentService $commentService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Todo $todo): JsonResponse
    {
        try {
            $comments = $this->commentService->getAllComments($todo, auth()->user());

            return $this->collection(
                TodoCommentResource::collection($comments),
                'data',
                'Comments retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoCommentRequest $request, Todo $todo): JsonResponse
    {
        try {
            $comment = $this->commentService->createComment(
                $todo,
                $request->validated(),
                auth()->user()
            );

            return $this->resource(
                new TodoCommentResource($comment->load(['user', 'replies'])),
                'Comment created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo, TodoComment $comment): JsonResponse
    {
        try {
            if ($comment->todo_id !== $todo->id) {
                return $this->error('Comment not found in this todo', 404);
            }

            $foundComment = $this->commentService->getCommentById($comment->id, auth()->user());

            if (!$foundComment) {
                return $this->error('Comment not found or unauthorized', 404);
            }

            return $this->resource(
                new TodoCommentResource($foundComment),
                'Comment retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTodoCommentRequest $request, Todo $todo, TodoComment $comment): JsonResponse
    {
        try {
            if ($comment->todo_id !== $todo->id) {
                return $this->error('Comment not found in this todo', 404);
            }

            $updatedComment = $this->commentService->updateComment(
                $comment,
                $request->validated(),
                auth()->user()
            );

            return $this->resource(
                new TodoCommentResource($updatedComment->load(['user', 'replies'])),
                'Comment updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo, TodoComment $comment): JsonResponse
    {
        try {
            if ($comment->todo_id !== $todo->id) {
                return $this->error('Comment not found in this todo', 404);
            }

            $this->commentService->deleteComment($comment, auth()->user());

            return $this->success(null, 'Comment deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
