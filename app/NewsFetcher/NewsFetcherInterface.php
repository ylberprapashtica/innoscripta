<?php

namespace App\NewsFetcher;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

interface NewsFetcherInterface
{
    public function getQuery(NewsFetcherQueryParameters $nfqp): string;

    /**
     * @return PromiseInterface[]
     */
    public function storeNewsAsync(string $term): array;

    public function storePage(Response $response): void;
}
