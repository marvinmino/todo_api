<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    /**
     * Get all activity logs for a user.
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllLogs(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return ActivityLog::where('user_id', $user->id)
            ->with('loggable')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get a log by ID.
     *
     * @param int $id
     * @param User $user
     * @return ActivityLog|null
     */
    public function getLogById(int $id, User $user): ?ActivityLog
    {
        return ActivityLog::where('id', $id)
            ->where('user_id', $user->id)
            ->with('loggable')
            ->first();
    }

    /**
     * Log an activity.
     *
     * @param User $user
     * @param mixed $loggable
     * @param string $action
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param string|null $description
     * @return ActivityLog
     */
    public function logActivity(
        User $user,
        $loggable,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user->id,
            'loggable_type' => get_class($loggable),
            'loggable_id' => $loggable->id,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}

