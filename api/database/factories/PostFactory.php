<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(10),
            'image' => $this->faker->imageUrl(),
            'link' => $this->faker->url(),
            'likes' => 0,
            'likes_users' => [],
            'shares_users' => [],
            'shares' => 0,
            'status' => 'active',
            'user_id' => User::factory(),
        ];
    }
}
