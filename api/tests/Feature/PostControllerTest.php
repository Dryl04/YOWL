<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_it_can_create_a_post()
    {
        $data = [
            'description' => 'Ceci est un test de post',
            'image' => 'https://example.com/image.jpg',
            'link' => 'https://example.com',
            'user_id' => $this->user->id,
        ];

        $response = $this->postJson('/api/v1/posts', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Post créé avec succès',
            ]);

        $this->assertDatabaseHas('posts', ['description' => $data['description']]);
    }

    public function test_it_can_get_all_posts()
    {
        Post::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'description', 'user_id']]
            ]);
    }

    public function test_it_can_get_a_specific_post()
    {
        $this->actingAs($this->user);
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => $post->id],
            ]);
    }


    public function test_it_can_update_a_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/v1/posts/{$post->id}", [
            'description' => 'Post modifié',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Post mis à jour avec succès',
            ]);

        $this->assertDatabaseHas('posts', ['description' => 'Post modifié']);
    }

    public function test_it_can_delete_a_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post supprimé avec succès']);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_it_can_like_and_unlike_a_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        // Like
        $likeResponse = $this->postJson("/api/v1/posts/{$post->id}/like");
        $likeResponse->assertStatus(200)
            ->assertJson(['message' => 'Post liké avec succès']);

        // Unlike
        $unlikeResponse = $this->postJson("/api/v1/posts/{$post->id}/unlike");
        $unlikeResponse->assertStatus(200)
            ->assertJson(['message' => 'Like retiré avec succès']);
    }

    public function test_it_can_share_a_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson("/api/v1/posts/{$post->id}/share");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Post partagé avec succès',
            ]);
    }

    public function test_it_can_get_posts_of_a_specific_user()
    {
        Post::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/users/{$this->user->id}/posts");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => [['id', 'description', 'user_id']]]);
    }
}
