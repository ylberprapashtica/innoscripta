<?php

namespace App\NewsFetcher;

use GuzzleHttp\Promise;

class NewsFetcher
{
    public function __construct(
        /** @var NewsFetcherInterface[] $newsFetchers */
        private iterable $newsFetchers
    )
    {
    }

    public function fetchNews(): mixed
    {
        $newsFetcherPromises = [];
        foreach ($this->newsFetchers as $newsFetcher) {
            $newsFetcherPromises = array_merge($newsFetcherPromises, $newsFetcher->storeNewsAsync('syria'));
        }

        $wait = Promise\Utils::all($newsFetcherPromises)->wait();
        return $wait;
    }

}
