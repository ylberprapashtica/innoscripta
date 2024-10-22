<?php

namespace App\NewsFetcher;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class NewsApiFetcher implements NewsFetcherInterface
{
    protected array $categories = [
        'business',
        'entertainment',
        'general',
        'health',
        'science',
        'sports',
        'technology',
    ];

    const NON_EXISTENT_ARTICLE = '[Removed]';

    public function __construct()
    {

    }

    public function fetchNews() {
        $response = Http::get('https://newsapi.org/v2/top-headlines?pageSize=100&language=en&apiKey=' . env('NEWS_API_KEY'));
        $this->handleResponse($response);
    }

    function handleResponse(\Illuminate\Http\Response $response){
        if($response->getStatusCode() === 200) {
            $articles = json_decode($response->body());
            foreach ($articles->articles as $article) {
                if($article->source->name !== self::NON_EXISTENT_ARTICLE) {
                    Article::upsert([
                        'publisher' => $article->source->name,
                        'author' => $article->author,
                        'title' => $article->title,
                        'description' => $article->description,
                        'url' => $article->url,
                        'urlToImage' => $article->urlToImage,
                        'publishedAt' => Carbon::create($article->publishedAt)->toDateTime(),
                        'content' => $article->content
                    ], ['title']);
                }
            }
        }else {

        }
    }

    protected function createQuery(array $parameters){
        return '?' . implode('&', $parameters);
    }
}
