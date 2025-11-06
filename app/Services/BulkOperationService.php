<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BulkOperationService
{
    /**
     * Bulk update todos.
     *
     * @param array $todoIds
     * @param array $data
     * @param User $user
     * @return int
     * @throws \Exception
     */
    public function bulkUpdate(array $todoIds, array $data, User $user): int
    {
        $todos = Todo::whereIn('id', $todoIds)
            ->whereHas('todoList', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        if ($todos->count() !== count($todoIds)) {
            throw new \Exception('Some todos not found or unauthorized', 404);
        }

        return Todo::whereIn('id', $todoIds)->withoutTrashed()->update($data);
    }

    /**
     * Bulk delete todos.
     *
     * @param array $todoIds
     * @param User $user
     * @return int
     * @throws \Exception
     */
    public function bulkDelete(array $todoIds, User $user): int
    {
        $todos = Todo::whereIn('id', $todoIds)
            ->whereHas('todoList', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        if ($todos->count() !== count($todoIds)) {
            throw new \Exception('Some todos not found or unauthorized', 404);
        }

        return Todo::whereIn('id', $todoIds)->delete();
    }

    /**
     * Bulk assign tags to todos.
     *
     * @param array $todoIds
     * @param array $tagIds
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function bulkAssignTags(array $todoIds, array $tagIds, User $user): void
    {
        $todos = Todo::whereIn('id', $todoIds)
            ->whereHas('todoList', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        if ($todos->count() !== count($todoIds)) {
            throw new \Exception('Some todos not found or unauthorized', 404);
        }

        foreach ($todos as $todo) {
            $todo->tags()->syncWithoutDetaching($tagIds);
        }
    }
}

