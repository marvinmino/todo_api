<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoListReminderRequest;
use App\Http\Resources\TodoListReminderResource;
use App\Http\Traits\ApiResponse;
use App\Models\TodoList;
use App\Models\TodoListReminder;
use App\Services\TodoListReminderService;
use Illuminate\Http\JsonResponse;

class TodoListReminderController extends Controller
{
    use ApiResponse;

    /**
     * Create a new TodoListReminderController instance.
     */
    public function __construct(
        protected TodoListReminderService $reminderService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(TodoList $todoList): JsonResponse
    {
        try {
            $reminders = $this->reminderService->getAllReminders($todoList, auth()->user());

            return $this->collection(
                TodoListReminderResource::collection($reminders),
                'data',
                'Reminders retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoListReminderRequest $request, TodoList $todoList): JsonResponse
    {
        try {
            $reminder = $this->reminderService->createReminder(
                $todoList,
                $request->validated(),
                auth()->user()
            );

            return $this->resource(
                new TodoListReminderResource($reminder),
                'Reminder created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TodoList $todoList, TodoListReminder $reminder): JsonResponse
    {
        try {
            $foundReminder = $this->reminderService->getReminderById(
                $todoList,
                $reminder->id,
                auth()->user()
            );

            return $this->resource(
                new TodoListReminderResource($foundReminder),
                'Reminder retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTodoListReminderRequest $request, TodoList $todoList, TodoListReminder $reminder): JsonResponse
    {
        try {
            $updatedReminder = $this->reminderService->updateReminder(
                $todoList,
                $reminder,
                $request->validated(),
                auth()->user()
            );

            return $this->resource(
                new TodoListReminderResource($updatedReminder),
                'Reminder updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TodoList $todoList, TodoListReminder $reminder): JsonResponse
    {
        try {
            $this->reminderService->deleteReminder($todoList, $reminder, auth()->user());

            return $this->success(null, 'Reminder deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
