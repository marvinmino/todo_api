<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BulkOperationController;
use App\Http\Controllers\Api\ExportImportController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TodoCommentController;
use App\Http\Controllers\Api\TodoController;
use App\Http\Controllers\Api\TodoListController;
use App\Http\Controllers\Api\TodoListNoteController;
use App\Http\Controllers\Api\TodoListReminderController;
use App\Http\Controllers\Api\TodoListShareController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (Authentication)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Statistics routes
    Route::get('/statistics', [StatisticsController::class, 'index']);
    Route::get('/statistics/dashboard', [StatisticsController::class, 'dashboard']);

    // Todo Lists routes
    Route::apiResource('todo-lists', TodoListController::class);
    
    // Todo List Archive routes
    Route::prefix('todo-lists')->group(function () {
        Route::post('/{todoList}/archive', [TodoListController::class, 'archive']);
        Route::post('/{id}/restore', [TodoListController::class, 'restore'])->where('id', '[0-9]+');
        Route::post('/{todoList}/toggle-favorite', [TodoListController::class, 'toggleFavorite']);
    });

    // Todo List Notes routes (nested under todo-lists)
    Route::prefix('todo-lists/{todoList}')->group(function () {
        Route::get('/notes', [TodoListNoteController::class, 'index']);
        Route::post('/notes', [TodoListNoteController::class, 'store']);
        Route::get('/notes/{note}', [TodoListNoteController::class, 'show']);
        Route::put('/notes/{note}', [TodoListNoteController::class, 'update']);
        Route::delete('/notes/{note}', [TodoListNoteController::class, 'destroy']);
    });

    // Todo List Reminders routes (nested under todo-lists)
    Route::prefix('todo-lists/{todoList}')->group(function () {
        Route::get('/reminders', [TodoListReminderController::class, 'index']);
        Route::post('/reminders', [TodoListReminderController::class, 'store']);
        Route::get('/reminders/{reminder}', [TodoListReminderController::class, 'show']);
        Route::put('/reminders/{reminder}', [TodoListReminderController::class, 'update']);
        Route::delete('/reminders/{reminder}', [TodoListReminderController::class, 'destroy']);
    });

    // Todo List Shares routes (nested under todo-lists)
    Route::prefix('todo-lists/{todoList}')->group(function () {
        Route::get('/shares', [TodoListShareController::class, 'index']);
        Route::post('/shares', [TodoListShareController::class, 'store']);
        Route::get('/shares/{share}', [TodoListShareController::class, 'show']);
        Route::put('/shares/{share}', [TodoListShareController::class, 'update']);
        Route::delete('/shares/{share}', [TodoListShareController::class, 'destroy']);
    });

    // Todos routes
    Route::apiResource('todos', TodoController::class);
    
    // Todo Archive routes
    Route::prefix('todos')->group(function () {
        Route::post('/{todo}/archive', [TodoController::class, 'archive']);
        Route::post('/{id}/restore', [TodoController::class, 'restore'])->where('id', '[0-9]+');
    });

    // Todo Comments routes (nested under todos)
    Route::prefix('todos/{todo}')->group(function () {
        Route::get('/comments', [TodoCommentController::class, 'index']);
        Route::post('/comments', [TodoCommentController::class, 'store']);
        Route::get('/comments/{comment}', [TodoCommentController::class, 'show']);
        Route::put('/comments/{comment}', [TodoCommentController::class, 'update']);
        Route::delete('/comments/{comment}', [TodoCommentController::class, 'destroy']);
    });

    // Tags routes
    Route::apiResource('tags', TagController::class);

    // Bulk Operations routes
    Route::prefix('todos')->group(function () {
        Route::post('/bulk-update', [BulkOperationController::class, 'bulkUpdate']);
        Route::post('/bulk-delete', [BulkOperationController::class, 'bulkDelete']);
        Route::post('/bulk-assign-tags', [BulkOperationController::class, 'bulkAssignTags']);
    });

    // Activity Logs routes
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
    Route::get('/activity-logs/{log}', [ActivityLogController::class, 'show']);

    // Export/Import routes
    Route::prefix('export')->group(function () {
        Route::post('/todos', [ExportImportController::class, 'exportTodos']);
        Route::post('/todo-lists', [ExportImportController::class, 'exportTodoLists']);
    });

    Route::prefix('import')->group(function () {
        Route::post('/todos', [ExportImportController::class, 'importTodos']);
        Route::post('/todo-lists', [ExportImportController::class, 'importTodoLists']);
    });
});
