<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Tests\TestCase;

class TagTest extends TestCase
{
    public function test_get_tags_returns_tags(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        Tag::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tags');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'name', 'color'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_create_tag_creates_tag(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $data = [
            'name' => 'Work',
            'color' => '#FF0000',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tags', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'color'],
            ]);

        $this->assertDatabaseHas('tags', [
            'user_id' => $user->id,
            'name' => 'Work',
        ]);
    }

    public function test_get_tag_by_id_returns_tag(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/tags/{$tag->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ],
            ]);
    }

    public function test_update_tag_updates_tag(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $tag = Tag::factory()->create(['user_id' => $user->id]);
        $data = ['name' => 'Updated Name', 'color' => '#00FF00'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/tags/{$tag->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Name',
                ],
            ]);
    }

    public function test_delete_tag_deletes_tag(): void
    {
        ['user' => $user, 'token' => $token] = $this->createAuthenticatedUser();
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/tags/{$tag->id}");

        $response->assertStatus(200);
        $this->assertNull(Tag::find($tag->id));
    }
}

