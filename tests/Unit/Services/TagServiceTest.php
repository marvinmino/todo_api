<?php

namespace Tests\Unit\Services;

use App\Models\Tag;
use App\Models\User;
use App\Services\TagService;
use Tests\TestCase;

class TagServiceTest extends TestCase
{
    private TagService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TagService();
    }

    public function test_get_all_tags_returns_user_tags(): void
    {
        $user = User::factory()->create();
        Tag::factory()->count(3)->create(['user_id' => $user->id]);
        Tag::factory()->count(2)->create(); // Other user's tags

        $result = $this->service->getAllTags($user);

        $this->assertCount(3, $result);
        $result->each(function ($tag) use ($user) {
            $this->assertEquals($user->id, $tag->user_id);
        });
    }

    public function test_create_tag_creates_tag(): void
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Work',
            'color' => '#FF0000',
        ];

        $result = $this->service->createTag($user, $data);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('Work', $result->name);
        $this->assertEquals('#FF0000', $result->color);
    }

    public function test_get_tag_by_id_returns_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $result = $this->service->getTagById($tag->id, $user);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($tag->id, $result->id);
    }

    public function test_get_tag_by_id_returns_null_for_other_user_tag(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $otherUser->id]);

        $result = $this->service->getTagById($tag->id, $user);

        $this->assertNull($result);
    }

    public function test_update_tag_updates_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $user->id]);
        $data = ['name' => 'Updated Name', 'color' => '#00FF00'];

        $result = $this->service->updateTag($tag, $data, $user);

        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals('#00FF00', $result->color);
    }

    public function test_update_tag_throws_exception_for_unauthorized(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $otherUser->id]);
        $data = ['name' => 'Updated Name'];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized');

        $this->service->updateTag($tag, $data, $user);
    }

    public function test_delete_tag_deletes_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $result = $this->service->deleteTag($tag, $user);

        $this->assertTrue($result);
        $this->assertNull(Tag::find($tag->id));
    }

    public function test_delete_tag_throws_exception_for_unauthorized(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $otherUser->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized');

        $this->service->deleteTag($tag, $user);
    }
}

