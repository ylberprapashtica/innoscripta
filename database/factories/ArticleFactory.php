<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
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
            'content' => fake()->realText(2000),
            'category_id' => fake()->numberBetween(1, Category::count())
        ];
    }
}
