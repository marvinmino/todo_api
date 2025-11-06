<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkAssignTagsRequest;
use App\Http\Requests\BulkDeleteTodoRequest;
use App\Http\Requests\BulkUpdateTodoRequest;
use App\Http\Traits\ApiResponse;
use App\Services\BulkOperationService;
use Illuminate\Http\JsonResponse;

class BulkOperationController extends Controller
{
    use ApiResponse;

    /**
     * Create a new BulkOperationController instance.
     */
    public function __construct(
        protected BulkOperationService $bulkOperationService
    ) {}

    /**
     * Bulk update todos.
     */
    public function bulkUpdate(BulkUpdateTodoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $todoIds = $data['todo_ids'];
            unset($data['todo_ids']);

            $updated = $this->bulkOperationService->bulkUpdate($todoIds, $data, auth()->user());

            return $this->success([
                'updated_count' => $updated,
            ], "Successfully updated {$updated} todo(s)");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Bulk delete todos.
     */
    public function bulkDelete(BulkDeleteTodoRequest $request): JsonResponse
    {
        try {
            $deleted = $this->bulkOperationService->bulkDelete(
                $request->validated()['todo_ids'],
                auth()->user()
            );

            return $this->success([
                'deleted_count' => $deleted,
            ], "Successfully deleted {$deleted} todo(s)");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Bulk assign tags to todos.
     */
    public function bulkAssignTags(BulkAssignTagsRequest $request): JsonResponse
    {
        try {
            $this->bulkOperationService->bulkAssignTags(
                $request->validated()['todo_ids'],
                $request->validated()['tag_ids'],
                auth()->user()
            );

            return $this->success(null, 'Tags assigned successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
