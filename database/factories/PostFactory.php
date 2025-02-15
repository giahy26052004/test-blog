<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence, // This will generate a random title
            'content' => $this->faker->paragraph, // Add other fields if needed
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
