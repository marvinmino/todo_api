<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property bool $is_favorite
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class TodoList extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_favorite',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    /**
     * Get the user that owns the todo list.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the todos for the todo list.
     *
     * @return HasMany
     */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    /**
     * Get the notes for the todo list.
     *
     * @return HasMany
     */
    public function notes(): HasMany
    {
        return $this->hasMany(TodoListNote::class);
    }

    /**
     * Get the reminders for the todo list.
     *
     * @return HasMany
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(TodoListReminder::class);
    }

    /**
     * Get the shares for the todo list.
     *
     * @return HasMany
     */
    public function shares(): HasMany
    {
        return $this->hasMany(TodoListShare::class);
    }

    /**
     * Get the users that this list is shared with.
     *
     * @return BelongsToMany
     */
    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'todo_list_shares')
            ->withPivot('permission')
            ->withTimestamps();
    }
}
