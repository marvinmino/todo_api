<?php

namespace App\Services;

use App\Models\TodoList;
use App\Models\TodoListNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TodoListNoteService
{
    /**
     * Get all notes for a todo list.
     *
     * @param TodoList $todoList
     * @param User $user
     * @return Collection
     * @throws \Exception
     */
    public function getAllNotes(TodoList $todoList, User $user): Collection
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $todoList->notes;
    }

    /**
     * Create a new note.
     *
     * @param TodoList $todoList
     * @param array $data
     * @param User $user
     * @return TodoListNote
     * @throws \Exception
     */
    public function createNote(TodoList $todoList, array $data, User $user): TodoListNote
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $todoList->notes()->create($data);
    }

    /**
     * Get a note by ID.
     *
     * @param TodoList $todoList
     * @param int $noteId
     * @param User $user
     * @return TodoListNote|null
     * @throws \Exception
     */
    public function getNoteById(TodoList $todoList, int $noteId, User $user): ?TodoListNote
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        $note = $todoList->notes()->find($noteId);

        if (!$note) {
            throw new \Exception('Note not found in this todo list', 404);
        }

        return $note;
    }

    /**
     * Update a note.
     *
     * @param TodoList $todoList
     * @param TodoListNote $note
     * @param array $data
     * @param User $user
     * @return TodoListNote
     * @throws \Exception
     */
    public function updateNote(TodoList $todoList, TodoListNote $note, array $data, User $user): TodoListNote
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        if ($note->todo_list_id !== $todoList->id) {
            throw new \Exception('Note not found in this todo list', 404);
        }

        $note->update($data);
        return $note;
    }

    /**
     * Delete a note.
     *
     * @param TodoList $todoList
     * @param TodoListNote $note
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function deleteNote(TodoList $todoList, TodoListNote $note, User $user): bool
    {
        if ($todoList->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        if ($note->todo_list_id !== $todoList->id) {
            throw new \Exception('Note not found in this todo list', 404);
        }

        return $note->delete();
    }
}

