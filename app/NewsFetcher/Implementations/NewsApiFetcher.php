<?php

namespace App\NewsFetcher\Implementations;

use App\Models\Article;
use App\NewsFetcher\ApiUrlGeneratorTrait;
use App\NewsFetcher\Exceptions\ApiUrlIsNotValidUrlException;
use App\NewsFetcher\Exceptions\EndpointIsNotRecognisedException;
use App\NewsFetcher\Exceptions\NewsFetcherResponseIsAnError;
use App\NewsFetcher\Exceptions\ParametersValidationFailException;
use App\NewsFetcher\NewsFetcherInterface;
use App\NewsFetcher\NewsFetcherQueryParameters;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class NewsApiFetcher implements NewsFetcherInterface
{
    use ApiUrlGeneratorTrait;

    const NON_EXISTENT_ARTICLE = '[Removed]';


    /**
     * @throws ParametersValidationFailException
     * @throws ConnectionException
     * @throws EndpointIsNotRecognisedException
     * @throws ApiUrlIsNotValidUrlException
     * @throws ValidationException
     */
    public function storeNewsAsync(string $term): array
    {
        $promise = $this->makeRequest(new NewsFetcherQueryParameters($term));
        return [self::class => $this->setStoreNewsAsyncPromiseHandler($promise)];
    }

    /**
     * @throws ParametersValidationFailException
     * @throws ConnectionException
     * @throws EndpointIsNotRecognisedException
     * @throws ApiUrlIsNotValidUrlException
     * @throws ValidationException
     */
    public function makeRequest(NewsFetcherQueryParameters $nfqp): PromiseInterface
    {
        $query = $this->getQuery($nfqp);
        return Http::async()->get($query);
    }

    /**
     * @param NewsFetcherQueryParameters $nfqp
     * @throws ParametersValidationFailException
     * @throws EndpointIsNotRecognisedException
     * @throws ApiUrlIsNotValidUrlException
     * @throws ValidationException
     */
    public function getQuery(NewsFetcherQueryParameters $nfqp): string
    {
        return $this->createQuery('top-headlines', [
            'apiKey' => env('NEWS_API_API_KEY'),
            'pageSize' => ($nfqp->useQueryMaxPageSize()) ? 100 : $nfqp->getPageSize(),
            'q' => $nfqp->getTerm(),
        ]);
    }

    private function setStoreNewsAsyncPromiseHandler(PromiseInterface $promise): PromiseInterface
    {
        return $promise->then(function ($response) {
            try {
                $this->storePage($response);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        });
    }

    /**
     * @throws NewsFetcherResponseIsAnError
     */
    function storePage(Response $response): void
    {
        if ($response->getStatusCode() === 200) {
            $articles = json_decode($response->body());
            foreach ($articles->articles as $article) {
                if ($article->source->name !== self::NON_EXISTENT_ARTICLE) {
                    Article::upsert([
                        'publisher' => $article->source->name,
                        'author' => $article->author,
                        'title' => $article->title,
                        'description' => $article->description,
                        'url' => $article->url,
                        'urlToImage' => $article->urlToImage,
                        'publishedAt' => Carbon::create($article->publishedAt)->toDateTime(),
                        'content' => $article->content,
                        'category_id' => null
                    ], ['title']);
                }
            }
        } else {
            throw new NewsFetcherResponseIsAnError($response->getStatusCode(), self::class);
        }
    }

    function getValidationRulesPerEndpoint(): array
    {
        return [
            'everything' => [
                'apiKey' => 'required|string',
                'q' => 'string|required',
                'searchIn' => 'sometimes|string|in:title,description,content',
                'sources' => 'sometimes|string',
                'domains ' => 'sometimes|string',
                'excludeDomains' => 'sometimes|string',
                'from' => 'sometimes|date',
                'to' => 'sometimes|date',
                'language' => 'sometimes|in:ar,de,en,es,fr,he,it,nl,no,pt,ru,sv,ud,zh',
                'sortBy' => 'sometimes|in:relevancy,popularity,publishedAt',
                'pageSize' => 'int',
                'page' => 'int',
            ],
            'top-headlines' => [
                'apiKey' => 'required|string',
                'country' => 'required_without_all:category,sources,q|in:ae,ar,at,au,be,bg,br,ca,ch,cn,co,cu,cz,de,eg,fr,gb,gr,hk,hu,id,ie,il,in,it,jp,kr,lt,lv,ma,mx,my,ng,nl,no,nz,ph,pl,pt,ro,rs,ru,sa,se,sg,si,sk,th,tr,tw,ua,us,ve,za',
                'category' => 'required_without_all:country,sources,q|in:business,entertainment,general,health,science,sports,technology',
                'sources' => 'required_without_all:country,category,q|string',
                'q' => 'required_without_all:country,category,sources|string',
                'pageSize' => 'int',
                'page' => 'int',
            ],
        ];
    }

    function getApiUrl(): string
    {
        return 'https://newsapi.org/v2/';
    }
}
