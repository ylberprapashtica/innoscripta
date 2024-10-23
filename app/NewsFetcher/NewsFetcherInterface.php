<?php

namespace App\NewsFetcher;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

interface NewsFetcherInterface
{
    public function getQuery(): string;

    public function fetchNews(): void;

    public function fetchNewsAsync(): PromiseInterface;

    public function handleResponse(Response $response): void;
}
