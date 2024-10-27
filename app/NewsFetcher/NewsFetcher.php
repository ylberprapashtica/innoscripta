<?php

namespace App\NewsFetcher;

use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;

class NewsFetcher
{
    public function __construct(
        /** @var NewsFetcherInterface[] $newsFetchers */
        private iterable $newsFetchers
    )
    {
    }

    public function fetchNews(string $term): mixed
    {
        return $this->gatherAllPromises($term)->wait();
    }

    /**
     * @return PromiseInterface
     */
    public function gatherAllPromises(string $term): PromiseInterface
    {
        $newsFetcherPromises = [];
        foreach ($this->newsFetchers as $newsFetcher) {
            $newsFetcherPromises = array_merge($newsFetcherPromises, $newsFetcher->storeNewsAsync($term));
        }

        return Promise\Utils::all($newsFetcherPromises);
    }

}
