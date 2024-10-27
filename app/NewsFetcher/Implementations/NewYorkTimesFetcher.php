<?php

namespace App\NewsFetcher\Implementations;

use App\Models\Article;
use App\Models\Category;
use App\NewsFetcher\ApiUrlGeneratorTrait;
use App\NewsFetcher\Exceptions\ApiUrlIsNotValidUrlException;
use App\NewsFetcher\Exceptions\EndpointIsNotRecognisedException;
use App\NewsFetcher\Exceptions\NewsFetcherResponseIsAnError;
use App\NewsFetcher\Exceptions\ParametersValidationFailException;
use App\NewsFetcher\NewsFetcherInterface;
use App\NewsFetcher\NewsFetcherQueryParameters;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use stdClass;

class NewYorkTimesFetcher implements NewsFetcherInterface
{
    use ApiUrlGeneratorTrait;

    //In a real project I would make an endpoint for end-users to configure this
    protected array $category_mapping = [
        'Arts' => Category::ART,
        'Automobiles' => Category::ENTERTAINMENT,
        'Autos' => Category::ENTERTAINMENT,
        'Blogs' => Category::ENTERTAINMENT,
        'Books' => Category::ENTERTAINMENT,
        'Booming' => Category::MISCELLANEOUS,
        'Business' => Category::BUSINESS,
        'Business Day' => Category::BUSINESS,
        'Corrections' => NULL,
        'Crosswords & Games' => Category::ENTERTAINMENT,
        'Crosswords/Games' => Category::ENTERTAINMENT,
        'Dining & Wine' => Category::TRAVEL,
        'Dining and Wine' => Category::TRAVEL,
        'Editors\' Notes' => Category::MISCELLANEOUS,
        'Education' => Category::EDUCATION,
        'Fashion & Style' => Category::FASHION,
        'Food' => Category::FOOD,
        'Front Page' => null,
        'Giving' => null,
        'Global Home' => Category::TRAVEL,
        'Great Homes & Destinations' => Category::TRAVEL,
        'Great Homes and Destinations' => Category::TRAVEL,
        'Health' => Category::HEALTH,
        'Home & Garden' => Category::MISCELLANEOUS,
        'Home and Garden' => Category::MISCELLANEOUS,
        'International Home' => Category::TRAVEL,
        'Job Market' => Category::BUSINESS,
        'Learning' => Category::EDUCATION,
        'Magazine' => Category::ENTERTAINMENT,
        'Movies' => Category::ENTERTAINMENT,
        'Multimedia' => Category::ENTERTAINMENT,
        'Multimedia/Photos' => Category::ENTERTAINMENT,
        'N.Y. / Region' => Category::MISCELLANEOUS,
        'N.Y./Region' => Category::MISCELLANEOUS,
        'NYRegion' => Category::MISCELLANEOUS,
        'NYT Now' => Category::MISCELLANEOUS,
        'National' => Category::MISCELLANEOUS,
        'New York' => Category::MISCELLANEOUS,
        'New York and Region' => Category::MISCELLANEOUS,
        'Obituaries' => null,
        'Olympics' => Category::SPORT,
        'Open' => Category::SPORT,
        'Opinion' => Category::MISCELLANEOUS,
        'Paid Death Notices' => null,
        'Public Editor' => null,
        'Real Estate' => Category::ECONOMY,
        'Science' => Category::SCIENCE,
        'Sports' => Category::SPORT,
        'Style' => Category::FASHION,
        'Sunday Magazine' => Category::MISCELLANEOUS,
        'Sunday Review' => Category::MISCELLANEOUS,
        'T Magazine' => Category::ENTERTAINMENT,
        'T:Style' => Category::FASHION,
        'Technology' => Category::SCIENCE,
        'The Public Editor' => Category::MISCELLANEOUS,
        'The Upshot' => null,
        'Theater' => Category::ART,
        'Times Topics' => Category::MISCELLANEOUS,
        'TimesMachine' => Category::MISCELLANEOUS,
        'Today\'s Headlines' => Category::MISCELLANEOUS,
        'Topics' => Category::MISCELLANEOUS,
        'Travel' => Category::TRAVEL,
        'U.S.' => Category::MISCELLANEOUS,
        'Universal' => Category::MISCELLANEOUS,
        'UrbanEye' => Category::TRAVEL,
        'Washington' => Category::MISCELLANEOUS,
        'Week in Review' => Category::MISCELLANEOUS,
        'World' => Category::TRAVEL,
        'Your Money' => Category::ECONOMY,
    ];

    /**
     * @param string $term
     * @throws ParametersValidationFailException
     * @throws ConnectionException
     * @throws EndpointIsNotRecognisedException
     * @throws ApiUrlIsNotValidUrlException
     * @throws ValidationException
     */
    public function storeNewsAsync(string $term): array
    {
        return [self::class => Http::async()->get($this->getQuery(NewsFetcherQueryParameters::create($term)))->then(function ($response) {
            try {
                $this->storePage($response);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        })
        ];
    }

    /**
     * @param array $parameters
     * @throws ParametersValidationFailException
     * @throws EndpointIsNotRecognisedException
     * @throws ApiUrlIsNotValidUrlException
     * @throws ValidationException
     */
    public function getQuery(NewsFetcherQueryParameters $nfqp): string
    {
        return $this->createQuery('articlesearch.json', [
            'api-key' => env('NEW_YORK_TIMES_API_KEY'),
            'q' => $nfqp->getTerm(),
        ]);
    }

    /**
     * Stores the articles of the page to databse
     * @throws NewsFetcherResponseIsAnError
     */
    function storePage(Response $response): void
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
                    'urlToImage' => $this->getUrlToImage($article->multimedia),
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

    private function getUrlToImage($multimedia): ?string
    {
        return (isset($multimedia['0']->url)) ? $multimedia['0']->url : null;
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
