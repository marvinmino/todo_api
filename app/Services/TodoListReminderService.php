<?php

namespace App\Services;

use App\Models\TodoList;
use App\Models\TodoListReminder;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TodoListReminderService
{
    /**
     * Get all reminders for a todo list.
     *
     * @param TodoList $todoList
     * @param User $user
     * @return Collection
     * @throws \Exception
     */
    public function getAllReminders(TodoList $todoList, User $user): Collection
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $todoList->reminders;
    }

    /**
     * Create a new reminder.
     *
     * @param TodoList $todoList
     * @param array $data
     * @param User $user
     * @return TodoListReminder
     * @throws \Exception
     */
    public function createReminder(TodoList $todoList, array $data, User $user): TodoListReminder
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $todoList->reminders()->create($data);
    }

    /**
     * Get a reminder by ID.
     *
     * @param TodoList $todoList
     * @param int $reminderId
     * @param User $user
     * @return TodoListReminder|null
     * @throws \Exception
     */
    public function getReminderById(TodoList $todoList, int $reminderId, User $user): ?TodoListReminder
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        $reminder = $todoList->reminders()->find($reminderId);

        if (!$reminder) {
            throw new \Exception('Reminder not found in this todo list', 404);
        }

        return $reminder;
    }

    /**
     * Update a reminder.
     *
     * @param TodoList $todoList
     * @param TodoListReminder $reminder
     * @param array $data
     * @param User $user
     * @return TodoListReminder
     * @throws \Exception
     */
    public function updateReminder(TodoList $todoList, TodoListReminder $reminder, array $data, User $user): TodoListReminder
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        if ($reminder->todo_list_id !== $todoList->id) {
            throw new \Exception('Reminder not found in this todo list', 404);
        }

        $reminder->update($data);
        return $reminder;
    }

    /**
     * Delete a reminder.
     *
     * @param TodoList $todoList
     * @param TodoListReminder $reminder
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function deleteReminder(TodoList $todoList, TodoListReminder $reminder, User $user): bool
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        if ($reminder->todo_list_id !== $todoList->id) {
            throw new \Exception('Reminder not found in this todo list', 404);
        }

        return $reminder->delete();
    }
}

