<?php

namespace App\NewsFetcher;

use App\Models\Article;
use App\Models\Category;
use App\NewsFetcher\Exceptions\ApiUrlIsNotValidUrlException;
use App\NewsFetcher\Exceptions\EndpointIsNotRecognisedException;
use App\NewsFetcher\Exceptions\NewsFetcherResponseIsAnError;
use App\NewsFetcher\Exceptions\ParametersValidationFailException;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class BBCNewsFetcher implements NewsFetcherInterface
{
    use ApiUrlGeneratorTrait;

    protected array $category_mapping = [
        'Latest' => null,
        'Only from the BBC' => null,
        'Must watch' => null,
        'Future Planet' => Category::ENVIRONMENT,
        'Thoughtful Travel' => Category::TRAVEL,
        'US election' => Category::POLITICS,
        'News' => null,
        'Sport' => Category::SPORT,
        'Business' => Category::BUSINESS,
        'Innovation' => Category::SCIENCE,
        'Video' => null,
        'Style' => Category::FASHION,
        'Culture' => Category::ENTERTAINMENT,
        'Travel' => Category::TRAVEL,
        'Earth' => Category::ENVIRONMENT,
        'World of Wonder' => Category::MISCELLANEOUS,
        'Latest on the US election' => Category::POLITICS,
        'Latest on the Israel-Gaza war' => Category::WAR,
        'Latest on the Ukraine war' => Category::WAR,
        'BBC InDepth' => null,
    ];

    /**
     * @throws ParametersValidationFailException
     * @throws NewsFetcherResponseIsAnError
     * @throws EndpointIsNotRecognisedException
     * @throws ApiUrlIsNotValidUrlException
     * @throws ValidationException
     */
    public function fetchNews(): void
    {
        $query = $this->createQuery('latest', [
            'lang' => 'english'
        ]);
        $response = Http::get($query);
        $this->handleResponse($response);
    }

    /**
     * @throws NewsFetcherResponseIsAnError
     */
    public function handleResponse(Response $response): void
    {
        if ($response->getStatusCode() === 200) {
            $articlesGrouped = json_decode($response->body());
            foreach ($articlesGrouped as $category => $articles) {
                if (is_array($articles)) {
                    foreach ($articles as $article) {
                        Article::upsert([
                            'publisher' => 'BBC',
                            'author' => null,
                            'title' => $article->title,
                            'description' => $article->summary,
                            'url' => $article->news_link,
                            'urlToImage' => $article->image_link,
                            'publishedAt' => null,
                            'content' => '',
                            'category_id' => Category::getArticleId($this->category_mapping[$category] ?? null)
                        ], ['title']);
                    }
                }
            }
        } else {
            throw new NewsFetcherResponseIsAnError($response->getStatusCode(), self::class);
        }
    }

    /**
     * @throws ParametersValidationFailException
     * @throws ConnectionException
     * @throws EndpointIsNotRecognisedException
     * @throws ApiUrlIsNotValidUrlException
     * @throws ValidationException
     */
    public function fetchNewsAsync(): PromiseInterface
    {
        $query = $this->createQuery('news', [
            'lang' => 'english'
        ]);
        return Http::async()->get($query)->then(function ($response) {
            $this->handleResponse($response);
        });
    }

    function getApiUrl(): string
    {
        return 'https://bbc-api.vercel.app/';
    }

    function getValidationRulesPerEndpoint(): array
    {
        return [
            'news' => [
                'lang' => 'required|in:arabic,azeri,bengali,english,portuguese,ukrainian'
            ],
            'latest' => [
                'lang' => 'required|in:arabic,azeri,bengali,english,portuguese,ukrainian'
            ]
        ];
    }
}
