<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\UserPreference;
use Carbon\Carbon;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Random\RandomException;
use Tests\TestCase;
use Tests\TestCases\ArticleUseCases;
use Tests\TestCases\UserTestCases;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;
    use UserTestCases;
    use ArticleUseCases;

    /** @test */
    public function itCanListArticles()
    {
        $this->seed(CategorySeeder::class);

        $numberOfArticles = 5;
        $this->createArticles(['title' => 'test'], $numberOfArticles);

        $response = $this->actingAsUser()->call('GET', '/api/articles', [
            'page' => 0,
            'per_page' => 10
        ]);

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('total', $numberOfArticles)
            ->etc()
        );
    }

    /**
     * @test
     * @throws RandomException
     */
    public function itCanListArticlesFilteredByDate(): void
    {
        $this->seed(CategorySeeder::class);
        $this->createArticlesPublishedAtTimeRange(
            Carbon::createFromFormat('d.m.Y', '01.01.2024'),
            Carbon::createFromFormat('d.m.Y', '31.01.2024'),
            ['title' => 'January'],
            5
        );
        $this->createArticlesPublishedAtTimeRange(
            Carbon::createFromFormat('d.m.Y', '01.02.2024'),
            Carbon::createFromFormat('d.m.Y', '28.02.2024'),
            ['title' => 'February'],
            5
        );

        $response = $this->actingAsUser()->call('GET', '/api/articles', [
            'page' => 0,
            'per_page' => 10,
            'older_than' => '2024-02-01',
            'newer_than' => '2024-02-28',
        ]);

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('total', 5)
            ->etc()
        );
    }


    /** @test */
    public function itCanGetOneArticle()
    {
        Category::factory()->create();

        $this->createArticles(['title' => 'test']);
        $response = $this->actingAsUser()->get('/api/articles/1');

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('title', 'test-0')
            ->etc()
        );
    }

    /** @test */
    public function itCanGetNewsFeed()
    {
        $preferences = [
            'categories' => [Category::TRAVEL, Category::EDUCATION],
            'authors' => ['Author1'],
            'publisher' => 'publisher1'
        ];
        $this->seed(CategorySeeder::class);
        $this->createArticles(['category_id' => Category::getArticleId(Category::TRAVEL)], 5);
        $this->createArticles(['category_id' => Category::getArticleId(Category::EDUCATION)], 5);
        $this->createArticles([
            'publisher' => $preferences['publisher'],
            'category_id' => Category::getArticleId(Category::POLITICS)
        ], 5);
        $this->createArticles([
            'author' => $preferences['authors'][0],
            'category_id' => Category::getArticleId(Category::POLITICS)
        ], 5);
        $this->createArticles([
            'author' => $preferences['authors'][0] . ' and Author2',
            'category_id' => Category::getArticleId(Category::POLITICS)
        ], 5);
        $this->createArticles([
            'category_id' => Category::getArticleId(Category::POLITICS)
        ], 10);

        $user = $this->createUser('user1');
        UserPreference::create(['user_id' => $user->id] + $preferences);

        $response = $this->actingAsUser($user)->call('GET', '/api/news-feed', [
            'page' => 0,
            'per_page' => 100,
        ]);
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('total', 25)
            ->etc()
        );
    }
}
