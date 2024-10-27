<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;
use ReflectionClass;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        return [
            'name' => self::getRandomCategory()
        ];
    }

    /**
     * @throws RandomException
     */
    public static function getRandomCategory()
    {
        $oClass = new ReflectionClass(Category::class);
        $categories = array_values($oClass->getConstants());
        //Model has created_at and updated_at as the last two categories, so we do not want to get those two therefore
        //the -3.
        return $categories[random_int(0, count($categories) - 3)];
    }
}
