<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    /**
     * Get all tags for a user.
     *
     * @param User $user
     * @return Collection
     */
    public function getAllTags(User $user): Collection
    {
        return $user->tags()->get();
    }

    /**
     * Create a new tag.
     *
     * @param User $user
     * @param array $data
     * @return Tag
     */
    public function createTag(User $user, array $data): Tag
    {
        return $user->tags()->create($data);
    }

    /**
     * Get a tag by ID.
     *
     * @param int $id
     * @param User $user
     * @return Tag|null
     */
    public function getTagById(int $id, User $user): ?Tag
    {
        return $user->tags()->find($id);
    }

    /**
     * Update a tag.
     *
     * @param Tag $tag
     * @param array $data
     * @param User $user
     * @return Tag
     * @throws \Exception
     */
    public function updateTag(Tag $tag, array $data, User $user): Tag
    {
        if ($tag->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        $tag->update($data);
        return $tag;
    }

    /**
     * Delete a tag.
     *
     * @param Tag $tag
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function deleteTag(Tag $tag, User $user): bool
    {
        if ($tag->user_id !== $user->id) {
            throw new \Exception('Unauthorized', 403);
        }

        return $tag->delete();
    }
}

