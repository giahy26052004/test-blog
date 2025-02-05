<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Category;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory(10000)->create()->each(function ($post) {
            $categories = Category::inRandomOrder()->take(rand(1, 5))->pluck('id');
            $post->categories()->attach($categories);
        });
    }
}
