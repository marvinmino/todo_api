<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    use ApiResponse;

    /**
     * Create a new StatisticsController instance.
     */
    public function __construct(
        protected StatisticsService $statisticsService
    ) {}

    /**
     * Get dashboard statistics.
     */
    public function dashboard(): JsonResponse
    {
        $stats = $this->statisticsService->getDashboardStats(auth()->user());

        return $this->success(['data' => $stats], 'Dashboard statistics retrieved successfully');
    }

    /**
     * Get general statistics.
     */
    public function index(): JsonResponse
    {
        $stats = $this->statisticsService->getDashboardStats(auth()->user());

        return $this->success(['data' => $stats], 'Statistics retrieved successfully');
    }
}
