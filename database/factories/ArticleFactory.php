<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'publisher' => fake()->company(),
            'author' => fake()->name(),
            'title' => fake()->sentence(),
            'description' => fake()->sentence(),
            'url' => fake()->url(),
            'urlToImage' => fake()->imageUrl(),
            'publishedAt' => fake()->date(),
            'content' => fake()->realText(2000)
        ];
    }
}
