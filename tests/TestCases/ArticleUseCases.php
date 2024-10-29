<?php

namespace Tests\TestCases;

use App\Models\Article;
use Carbon\Carbon;
use Random\RandomException;

trait ArticleUseCases
{
    const ARTICLE_ATTRIBUTES_TO_CHANGE = [
        'title',
        'description'
    ];

    /**
     * @throws RandomException
     */
    private function createArticlesPublishedAtTimeRange(
        Carbon $fromDate,
        Carbon $toDate,
        array  $attributes = [],
        int    $numberOfArticlesToCreate = 1
    ): void
    {
        $randomDateFromRange = $fromDate->addHours(random_int(1, $fromDate->diffInHours($toDate)));
        $this->createArticles(
            array_merge($attributes, ['publishedAt' => $randomDateFromRange]),
            $numberOfArticlesToCreate
        );
    }

    private function createArticles(
        array $attributes = [],
        int   $numberOfArticlesToCreate = 1
    ): void
    {
        for ($i = 0; $i < $numberOfArticlesToCreate; $i++) {
            Article::factory()->create($this->changeCurrentArticleValues($attributes, $i));
        }
    }

    private function changeCurrentArticleValues(array $attributes, int $count): array
    {
        $subset = array_intersect_key($attributes, array_flip(self::ARTICLE_ATTRIBUTES_TO_CHANGE));
        return array_merge($attributes, array_map(fn($value) => $value . '-' . $count, $subset));
    }
}
