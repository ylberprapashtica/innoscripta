<?php

namespace App\NewsFetcher;

use Illuminate\Http\Response;

interface NewsFetcherInterface
{
    public function fetchNews();

    function handleResponse(Response $response);
}
