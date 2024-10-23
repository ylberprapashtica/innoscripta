<?php

namespace App\NewsFetcher;

use App\Models\Article;
use App\Models\Category;
use App\NewsFetcher\Exceptions\ApiUrlIsNotValidUrlException;
use App\NewsFetcher\Exceptions\EndpointIsNotRecognisedException;
use App\NewsFetcher\Exceptions\NewsFetcherResponseIsAnError;
use App\NewsFetcher\Exceptions\ParametersValidationFailException;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use stdClass;

class NewYorkTimesFetcher implements NewsFetcherInterface
{
    use ApiUrlGeneratorTrait;

    protected array $category_mapping = [
        'Arts' => Category::MISCELLANEOUS,
        'Business' => Category::BUSINESS,
        'Business Day' => Category::BUSINESS,
        'Education' => Category::EDUCATION,
        'Fashion & Style' => Category::FASHION,
        'Health' => Category::HEALTH,
        'Job Market' => Category::BUSINESS,
        'Olympics' => Category::SPORT,
        'Open' => Category::SPORT,
        'Opinion' => Category::ENTERTAINMENT,
        'T Magazine' => null,
        'Theater' => Category::ENTERTAINMENT,
        'Universal' => null,
        'UrbanEye' => Category::MISCELLANEOUS,
        'World' => Category::TRAVEL,
        'Your Money' => Category::ECONOMY,
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
        $response = Http::get($this->getQuery());
        $this->handleResponse($response);
    }

    /**
     * @throws ParametersValidationFailException
     * @throws EndpointIsNotRecognisedException
     * @throws ApiUrlIsNotValidUrlException
     * @throws ValidationException
     */
    public function getQuery(): string
    {
        return $this->createQuery('articlesearch.json', [
            'api-key' => env('NEW_YORK_TIMES_API_KEY'),
            'q' => 'general',
        ]);
    }

    /**
     * @throws NewsFetcherResponseIsAnError
     */
    function handleResponse(Response $response): void
    {
        if ($response->getStatusCode() === 200) {
            $articles = json_decode($response->body());
            foreach ($articles->response->docs as $article) {
                Article::upsert([
                    'publisher' => $article->source,
                    'author' => $this->getAuthorsFromByline($article->byline),
                    'title' => $article->abstract,
                    'description' => $article->snippet,
                    'url' => $article->web_url,
                    'urlToImage' => $article->multimedia['0']->url,
                    'publishedAt' => Carbon::create($article->pub_date)->toDateTime(),
                    'content' => $article->lead_paragraph,
                    'category_id' => Category::getArticleId($this->category_mapping[$article->news_desk] ?? null)
                ], ['title']);
            }
        } else {
            throw new NewsFetcherResponseIsAnError($response->getStatusCode(), self::class);
        }
    }

    private function getAuthorsFromByline(stdClass $byLine): string
    {
        $authors = '';
        foreach ($byLine->person as $author) {
            $authors .= $author->firstname . ' ' . $author->lastname . ',';
        }
        return substr($authors, 0, -1);
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
        return Http::async()->get($this->getQuery())->then(function ($response) {
            $this->handleResponse($response);
        });
    }

    function getValidationRulesPerEndpoint(): array
    {
        return [
            'articlesearch.json' => [
                'api-key' => 'required|string',
                'q' => 'string|required',
                'fq' => 'string',
            ],
        ];
    }

    function getApiUrl(): string
    {
        return 'https://api.nytimes.com/svc/search/v2/';
    }
}
