<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class StatisticsService
{
    /**
     * Get dashboard statistics for a user.
     *
     * @param User $user
     * @return array
     */
    public function getDashboardStats(User $user): array
    {
        $todoLists = $user->todoLists();
        $todos = $user->todoLists()->with('todos')->get()->pluck('todos')->flatten();

        return [
            'total_todo_lists' => $todoLists->count(),
            'total_todos' => $todos->count(),
            'completed_todos' => $todos->where('completed', true)->count(),
            'pending_todos' => $todos->where('completed', false)->count(),
            'completion_rate' => $todos->count() > 0 
                ? round(($todos->where('completed', true)->count() / $todos->count()) * 100, 2)
                : 0,
            'todos_by_priority' => [
                'low' => $todos->where('priority', 'low')->count(),
                'medium' => $todos->where('priority', 'medium')->count(),
                'high' => $todos->where('priority', 'high')->count(),
                'urgent' => $todos->where('priority', 'urgent')->count(),
            ],
            'overdue_todos' => $todos->filter(function ($todo) {
                return $todo->due_date && $todo->due_date->isPast() && !$todo->completed;
            })->count(),
            'due_today' => $todos->filter(function ($todo) {
                return $todo->due_date && $todo->due_date->isToday() && !$todo->completed;
            })->count(),
            'due_this_week' => $todos->filter(function ($todo) {
                return $todo->due_date 
                    && $todo->due_date->isBetween(Carbon::now(), Carbon::now()->addWeek())
                    && !$todo->completed;
            })->count(),
            'favorite_lists' => $todoLists->where('is_favorite', true)->count(),
        ];
    }
}

