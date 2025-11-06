<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetActivityLogsRequest;
use App\Http\Resources\ActivityLogResource;
use App\Http\Traits\ApiResponse;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;

class ActivityLogController extends Controller
{
    use ApiResponse;

    /**
     * Create a new ActivityLogController instance.
     */
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(GetActivityLogsRequest $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $logs = $this->activityLogService->getAllLogs(auth()->user(), $perPage);

        $collection = ActivityLogResource::collection($logs);
        return $this->collection(
            $collection,
            'data',
            'Activity logs retrieved successfully',
            200,
            $logs
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityLog $log): JsonResponse
    {
        $foundLog = $this->activityLogService->getLogById($log->id, auth()->user());

        if (!$foundLog) {
            return $this->error('Activity log not found or unauthorized', 404);
        }

        return $this->resource(
            new ActivityLogResource($foundLog),
            'Activity log retrieved successfully'
        );
    }
}
