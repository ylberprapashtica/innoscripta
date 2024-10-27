<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    const WAR = 'war';
    const GOVERNMENT = 'government';
    const POLITICS = 'politics';
    const EDUCATION = 'education';
    const HEALTH = 'health';
    const ECONOMY = 'economy';
    const BUSINESS = 'business';
    const FASHION = 'fashion';
    const SPORT = 'sport';
    const ENTERTAINMENT = 'entertainment';
    const ENVIRONMENT = 'environment';
    const MISCELLANEOUS = 'miscellaneous';
    const TRAVEL = 'travel';
    const SCIENCE = 'science';
    const ART = 'art';
    const FOOD = 'food';
    protected $fillable = [
        'name'
    ];

    public static function getArticleId(?string $articleName): ?int
    {
        $id = array_search($articleName, self::getCategories());
        return ($id) ?: null;
    }

    public static function getCategories()
    {
        return once(function () {
            $simplifiedArray = [];
            foreach (self::query()->get() as $category) {
                $simplifiedArray[$category->id] = $category->name;
            }

            return $simplifiedArray;
        });
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

}
