<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\TodoComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TodoCommentService
{
    /**
     * Get all comments for a todo.
     *
     * @param Todo $todo
     * @param User $user
     * @return Collection
     * @throws \Exception
     */
    public function getAllComments(Todo $todo, User $user): Collection
    {
        if ($todo->todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $todo->comments()->with(['user', 'replies'])->whereNull('parent_id')->get();
    }

    /**
     * Create a comment.
     *
     * @param Todo $todo
     * @param array $data
     * @param User $user
     * @return TodoComment
     * @throws \Exception
     */
    public function createComment(Todo $todo, array $data, User $user): TodoComment
    {
        if ($todo->todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        $data['user_id'] = $user->id;
        return $todo->comments()->create($data);
    }

    /**
     * Get a comment by ID.
     *
     * @param int $id
     * @param User $user
     * @return TodoComment|null
     */
    public function getCommentById(int $id, User $user): ?TodoComment
    {
        return TodoComment::where('id', $id)
            ->whereHas('todo.todoList', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['user', 'replies'])
            ->first();
    }

    /**
     * Update a comment.
     *
     * @param TodoComment $comment
     * @param array $data
     * @param User $user
     * @return TodoComment
     * @throws \Exception
     */
    public function updateComment(TodoComment $comment, array $data, User $user): TodoComment
    {
        if ($comment->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        $comment->update($data);
        return $comment;
    }

    /**
     * Delete a comment.
     *
     * @param TodoComment $comment
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function deleteComment(TodoComment $comment, User $user): bool
    {
        if ($comment->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $comment->delete();
    }
}

