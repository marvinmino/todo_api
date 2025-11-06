<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $todo_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TodoComment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'todo_id',
        'user_id',
        'parent_id',
        'comment',
    ];

    /**
     * Get the todo that owns the comment.
     *
     * @return BelongsTo
     */
    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

    /**
     * Get the user that created the comment.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment.
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TodoComment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment.
     *
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(TodoComment::class, 'parent_id');
    }
}
