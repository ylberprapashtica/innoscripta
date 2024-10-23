<?php

namespace App\NewsFetcher;

use GuzzleHttp\Promise;

class NewsFetcher
{
    public function __construct(
        /** @var NewsFetcherInterface[] $newsFetchers */
        private readonly iterable $newsFetchers
    )
    {
    }

    public function fetchNews()
    {
        $newsFetcherPromises = [];
        foreach ($this->newsFetchers as $newsFetcher) {
            $newsFetcherPromises[] = $newsFetcher->fetchNewsAsync();
        }

        $results = Promise\Utils::all($newsFetcherPromises)->wait();
    }

}
