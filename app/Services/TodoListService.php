<?php

namespace App\Services;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TodoListService
{
    /**
     * Get all todo lists for a user with pagination, sorting, filtering, and search.
     *
     * @param User $user
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllTodoLists(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $user->todoLists()->with(['todos', 'notes', 'reminders', 'shares']);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get all todo lists for a user (without pagination).
     *
     * @param User $user
     * @param array $filters
     * @return Collection
     */
    public function getAllTodoListsCollection(User $user, array $filters = []): Collection
    {
        $query = $user->todoLists()->with(['todos', 'notes', 'reminders', 'shares']);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->get();
    }

    /**
     * Apply filters to query.
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    protected function applyFilters($query, array $filters)
    {
        // Filter by favorites
        if (isset($filters['is_favorite'])) {
            $isFavorite = filter_var($filters['is_favorite'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isFavorite !== null) {
                $query->where('is_favorite', $isFavorite);
            }
        }

        // Filter by shared
        if (isset($filters['shared']) && $filters['shared']) {
            $query->whereHas('shares');
        }

        // Search
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Archive filter
        if (isset($filters['archived'])) {
            if ($filters['archived']) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }
        } else {
            $query->withoutTrashed();
        }

        return $query;
    }

    /**
     * Create a new todo list.
     *
     * @param User $user
     * @param array $data
     * @return TodoList
     */
    public function createTodoList(User $user, array $data): TodoList
    {
        $todoList = $user->todoLists()->create($data);
        return $todoList->load(['todos', 'notes', 'reminders', 'shares']);
    }

    /**
     * Get a todo list by ID.
     *
     * @param int $id
     * @param User $user
     * @return TodoList|null
     */
    public function getTodoListById(int $id, User $user): ?TodoList
    {
        return $user->todoLists()->with(['todos', 'notes', 'reminders', 'shares'])->find($id);
    }

    /**
     * Update a todo list.
     *
     * @param TodoList $todoList
     * @param array $data
     * @return TodoList
     */
    public function updateTodoList(TodoList $todoList, array $data): TodoList
    {
        $todoList->update($data);
        return $todoList->load(['todos', 'notes', 'reminders', 'shares']);
    }

    /**
     * Delete a todo list.
     *
     * @param TodoList $todoList
     * @return bool
     */
    public function deleteTodoList(TodoList $todoList): bool
    {
        return $todoList->delete();
    }

    /**
     * Archive a todo list.
     *
     * @param TodoList $todoList
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function archiveTodoList(TodoList $todoList, User $user): bool
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $todoList->delete();
    }

    /**
     * Restore a todo list.
     *
     * @param int $id
     * @param User $user
     * @return TodoList
     * @throws \Exception
     */
    public function restoreTodoList(int $id, User $user): TodoList
    {
        $todoList = TodoList::onlyTrashed()
            ->where('user_id', $user->id)
            ->find($id);

        if (!$todoList) {
            throw new \Exception('Todo list not found or unauthorized', 404);
        }

        $todoList->restore();
        return $todoList->load(['todos', 'notes', 'reminders', 'shares']);
    }

    /**
     * Toggle favorite status.
     *
     * @param TodoList $todoList
     * @param User $user
     * @return TodoList
     * @throws \Exception
     */
    public function toggleFavorite(TodoList $todoList, User $user): TodoList
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        $todoList->update(['is_favorite' => !$todoList->is_favorite]);
        return $todoList->load(['todos', 'notes', 'reminders', 'shares']);
    }

    /**
     * Check if todo list belongs to user.
     *
     * @param TodoList $todoList
     * @param User $user
     * @return bool
     */
    public function belongsToUser(TodoList $todoList, User $user): bool
    {
        return $todoList->user_id === $user->id;
    }
}
