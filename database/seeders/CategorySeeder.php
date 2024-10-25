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
            'name' => Category::WAR,
        ]);

        Category::factory()->create([
            'name' => Category::GOVERNMENT,
        ]);

        Category::factory()->create([
            'name' => Category::POLITICS,
        ]);

        Category::factory()->create([
            'name' => Category::EDUCATION,
        ]);

        Category::factory()->create([
            'name' => Category::HEALTH,
        ]);

        Category::factory()->create([
            'name' => Category::ECONOMY,
        ]);

        Category::factory()->create([
            'name' => Category::BUSINESS,
        ]);

        Category::factory()->create([
            'name' => Category::FASHION,
        ]);

        Category::factory()->create([
            'name' => Category::SPORT,
        ]);

        Category::factory()->create([
            'name' => Category::ENTERTAINMENT,
        ]);

        Category::factory()->create([
            'name' => Category::ENVIRONMENT,
        ]);

        Category::factory()->create([
            'name' => Category::TRAVEL,
        ]);

        Category::factory()->create([
            'name' => Category::SCIENCE,
        ]);

        Category::factory()->create([
            'name' => Category::MISCELLANEOUS,
        ]);

        Category::factory()->create([
            'name' => Category::ART,
        ]);

        Category::factory()->create([
            'name' => Category::FOOD,
        ]);
    }
}
