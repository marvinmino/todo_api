<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class TodoService
{
    /**
     * Get all todos for a user with pagination, sorting, filtering, and search.
     *
     * @param User $user
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllTodos(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Todo::whereHas('todoList', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['todoList', 'tags', 'parent', 'subTodos']);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get all todos for a user (without pagination).
     *
     * @param User $user
     * @param array $filters
     * @return Collection
     */
    public function getAllTodosCollection(User $user, array $filters = []): Collection
    {
        $query = Todo::whereHas('todoList', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['todoList', 'tags', 'parent', 'subTodos']);

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
        // Filter by completed status
        if (isset($filters['completed'])) {
            $completed = filter_var($filters['completed'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($completed !== null) {
                $query->where('completed', $completed);
            }
        }

        // Filter by priority
        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Filter by due date range
        if (isset($filters['due_date_from'])) {
            $query->where('due_date', '>=', $filters['due_date_from']);
        }

        if (isset($filters['due_date_to'])) {
            $query->where('due_date', '<=', $filters['due_date_to']);
        }

        // Filter by overdue
        if (isset($filters['overdue']) && $filters['overdue']) {
            $query->where('due_date', '<', now())
                ->where('completed', false);
        }

        // Filter by due today
        if (isset($filters['due_today']) && $filters['due_today']) {
            $query->whereDate('due_date', today())
                ->where('completed', false);
        }

        // Filter by todo list
        if (isset($filters['todo_list_id'])) {
            $query->where('todo_list_id', $filters['todo_list_id']);
        }

        // Filter by parent (sub-todos only)
        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        // Filter by tags
        if (isset($filters['tag_ids']) && is_array($filters['tag_ids'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->whereIn('tags.id', $filters['tag_ids']);
            });
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
     * Create a new todo.
     *
     * @param array $data
     * @param User $user
     * @return Todo
     * @throws \Exception
     */
    public function createTodo(array $data, User $user): Todo
    {
        // Verify the todo list belongs to the authenticated user
        $todoList = TodoList::where('id', $data['todo_list_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$todoList) {
            throw new \Exception('Todo list not found or unauthorized', 404);
        }

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            $imagePath = $data['image']->store('todo-images', 'public');
            $data['image_path'] = $imagePath;
        }

        // Handle tags
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['image'], $data['tag_ids']);

        $todo = Todo::create($data);

        // Attach tags
        if (!empty($tagIds)) {
            $todo->tags()->attach($tagIds);
        }

        return $todo->load(['todoList', 'tags', 'parent', 'subTodos']);
    }

    /**
     * Get a todo by ID.
     *
     * @param int $id
     * @param User $user
     * @return Todo|null
     */
    public function getTodoById(int $id, User $user): ?Todo
    {
        return Todo::whereHas('todoList', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['todoList', 'tags', 'parent', 'subTodos', 'comments'])->find($id);
    }

    /**
     * Update a todo.
     *
     * @param Todo $todo
     * @param array $data
     * @param User $user
     * @return Todo
     * @throws \Exception
     */
    public function updateTodo(Todo $todo, array $data, User $user): Todo
    {
        // Ensure the todo belongs to the authenticated user
        if ($todo->todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            // Delete old image if exists
            if ($todo->image_path) {
                Storage::disk('public')->delete($todo->image_path);
            }

            $imagePath = $data['image']->store('todo-images', 'public');
            $data['image_path'] = $imagePath;
        }

        // Handle tags
        if (isset($data['tag_ids'])) {
            $todo->tags()->sync($data['tag_ids']);
            unset($data['tag_ids']);
        }

        unset($data['image']);

        $todo->update($data);
        return $todo->load(['todoList', 'tags', 'parent', 'subTodos']);
    }

    /**
     * Delete a todo.
     *
     * @param Todo $todo
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function deleteTodo(Todo $todo, User $user): bool
    {
        // Ensure the todo belongs to the authenticated user
        if ($todo->todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        // Delete image if exists
        if ($todo->image_path) {
            Storage::disk('public')->delete($todo->image_path);
        }

        return $todo->delete();
    }

    /**
     * Archive a todo.
     *
     * @param Todo $todo
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function archiveTodo(Todo $todo, User $user): bool
    {
        if ($todo->todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $todo->delete();
    }

    /**
     * Restore a todo.
     *
     * @param int $id
     * @param User $user
     * @return Todo
     * @throws \Exception
     */
    public function restoreTodo(int $id, User $user): Todo
    {
        $todo = Todo::onlyTrashed()
            ->whereHas('todoList', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->find($id);

        if (!$todo) {
            throw new \Exception('Todo not found or unauthorized', 404);
        }

        $todo->restore();
        return $todo->load(['todoList', 'tags', 'parent', 'subTodos']);
    }

    /**
     * Check if todo belongs to user.
     *
     * @param Todo $todo
     * @param User $user
     * @return bool
     */
    public function belongsToUser(Todo $todo, User $user): bool
    {
        return $todo->todoList->user_id === $user->id;
    }
}
