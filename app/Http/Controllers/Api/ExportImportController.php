<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportTodoListsRequest;
use App\Http\Requests\ImportTodosRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Todo;
use App\Models\TodoList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ExportImportController extends Controller
{
    use ApiResponse;

    /**
     * Export todos to JSON.
     */
    public function exportTodos(): JsonResponse
    {
        $user = auth()->user();
        $todos = Todo::whereHas('todoList', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['todoList', 'tags', 'parent', 'subTodos', 'comments'])
        ->get();

        return $this->success([
            'todos' => $todos,
            'exported_at' => now()->toDateTimeString(),
        ], 'Todos exported successfully');
    }

    /**
     * Export todo lists to JSON.
     */
    public function exportTodoLists(): JsonResponse
    {
        $user = auth()->user();
        $todoLists = TodoList::where('user_id', $user->id)
            ->with(['todos', 'notes', 'reminders', 'shares'])
            ->get();

        return $this->success([
            'todo_lists' => $todoLists,
            'exported_at' => now()->toDateTimeString(),
        ], 'Todo lists exported successfully');
    }

    /**
     * Import todos from JSON.
     */
    public function importTodos(ImportTodosRequest $request): JsonResponse
    {
        $user = auth()->user();
        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->todos as $todoData) {
                // Verify todo list belongs to user
                $todoList = TodoList::where('id', $todoData['todo_list_id'])
                    ->where('user_id', $user->id)
                    ->first();

                if (!$todoList) {
                    $errors[] = "Todo list {$todoData['todo_list_id']} not found or unauthorized";
                    continue;
                }

                $todo = Todo::create([
                    'todo_list_id' => $todoData['todo_list_id'],
                    'title' => $todoData['title'],
                    'description' => $todoData['description'] ?? null,
                    'completed' => $todoData['completed'] ?? false,
                    'priority' => $todoData['priority'] ?? 'medium',
                    'due_date' => $todoData['due_date'] ?? null,
                    'parent_id' => $todoData['parent_id'] ?? null,
                ]);

                // Attach tags if provided
                if (isset($todoData['tag_ids']) && is_array($todoData['tag_ids'])) {
                    $todo->tags()->attach($todoData['tag_ids']);
                }

                $imported++;
            }

            DB::commit();

            return $this->success([
                'imported_count' => $imported,
                'errors' => $errors,
            ], "Successfully imported {$imported} todo(s)");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Import todo lists from JSON.
     */
    public function importTodoLists(ImportTodoListsRequest $request): JsonResponse
    {
        $user = auth()->user();
        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->todo_lists as $listData) {
                $todoList = TodoList::create([
                    'user_id' => $user->id,
                    'title' => $listData['title'],
                    'description' => $listData['description'] ?? null,
                    'is_favorite' => $listData['is_favorite'] ?? false,
                ]);

                // Import todos if provided
                if (isset($listData['todos']) && is_array($listData['todos'])) {
                    foreach ($listData['todos'] as $todoData) {
                        $todo = Todo::create([
                            'todo_list_id' => $todoList->id,
                            'title' => $todoData['title'],
                            'description' => $todoData['description'] ?? null,
                            'completed' => $todoData['completed'] ?? false,
                            'priority' => $todoData['priority'] ?? 'medium',
                            'due_date' => $todoData['due_date'] ?? null,
                            'parent_id' => $todoData['parent_id'] ?? null,
                        ]);

                        // Attach tags if provided
                        if (isset($todoData['tag_ids']) && is_array($todoData['tag_ids'])) {
                            $todo->tags()->attach($todoData['tag_ids']);
                        }
                    }
                }

                $imported++;
            }

            DB::commit();

            return $this->success([
                'imported_count' => $imported,
                'errors' => $errors,
            ], "Successfully imported {$imported} todo list(s)");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
}
