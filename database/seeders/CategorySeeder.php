<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->create([
            'name' => 'war',
        ]);

        Category::factory()->create([
            'name' => 'government',
        ]);

        Category::factory()->create([
            'name' => 'politics',
        ]);

        Category::factory()->create([
            'name' => 'education',
        ]);

        Category::factory()->create([
            'name' => 'health',
        ]);

        Category::factory()->create([
            'name' => 'economy',
        ]);

        Category::factory()->create([
            'name' => 'business',
        ]);

        Category::factory()->create([
            'name' => 'fashion',
        ]);

        Category::factory()->create([
            'name' => 'sport',
        ]);

        Category::factory()->create([
            'name' => 'entertainment',
        ]);

        Category::factory()->create([
            'name' => 'environment',
        ]);

        Category::factory()->create([
            'name' => 'miscellaneous',
        ]);
    }
}
