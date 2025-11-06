<?php

namespace App\Services;

use App\Models\TodoList;
use App\Models\TodoListShare;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TodoListShareService
{
    /**
     * Get all shares for a todo list.
     *
     * @param TodoList $todoList
     * @param User $user
     * @return Collection
     * @throws \Exception
     */
    public function getAllShares(TodoList $todoList, User $user): Collection
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $todoList->shares()->with('user')->get();
    }

    /**
     * Share a todo list with a user.
     *
     * @param TodoList $todoList
     * @param int $userId
     * @param string $permission
     * @param User $owner
     * @return TodoListShare
     * @throws \Exception
     */
    public function shareTodoList(TodoList $todoList, int $userId, string $permission, User $owner): TodoListShare
    {
        if ($todoList->user_id !== $owner->id) {
            throw new \Exception('Unauthorized', 403);
        }

        if ($userId === $owner->id) {
            throw new \Exception('Cannot share with yourself', 400);
        }

        return TodoListShare::firstOrCreate(
            [
                'todo_list_id' => $todoList->id,
                'user_id' => $userId,
            ],
            [
                'permission' => $permission,
            ]
        );
    }

    /**
     * Update share permission.
     *
     * @param TodoListShare $share
     * @param string $permission
     * @param User $user
     * @return TodoListShare
     * @throws \Exception
     */
    public function updateShare(TodoListShare $share, string $permission, User $user): TodoListShare
    {
        if ($share->todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        $share->update(['permission' => $permission]);
        return $share;
    }

    /**
     * Remove share.
     *
     * @param TodoListShare $share
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function removeShare(TodoListShare $share, User $user): bool
    {
        if ($share->todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $share->delete();
    }
}

