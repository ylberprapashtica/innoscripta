<?php

namespace App\NewsFetcher;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

interface NewsFetcherInterface
{
    public function fetchNews(): void;

    public function fetchNewsAsync(): PromiseInterface;

    function handleResponse(Response $response): void;
}
