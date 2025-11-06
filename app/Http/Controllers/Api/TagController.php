<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Http\Traits\ApiResponse;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    use ApiResponse;

    /**
     * Create a new TagController instance.
     */
    public function __construct(
        protected TagService $tagService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tags = $this->tagService->getAllTags(auth()->user());

        return $this->collection(
            TagResource::collection($tags),
            'data',
            'Tags retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request): JsonResponse
    {
        try {
            $tag = $this->tagService->createTag(auth()->user(), $request->validated());

            return $this->resource(
                new TagResource($tag),
                'Tag created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag): JsonResponse
    {
        $foundTag = $this->tagService->getTagById($tag->id, auth()->user());

        if (!$foundTag || $foundTag->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        return $this->resource(
            new TagResource($foundTag),
            'Tag retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        try {
            $updatedTag = $this->tagService->updateTag($tag, $request->validated(), auth()->user());

            return $this->resource(
                new TagResource($updatedTag),
                'Tag updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): JsonResponse
    {
        try {
            $this->tagService->deleteTag($tag, auth()->user());

            return $this->success(null, 'Tag deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
