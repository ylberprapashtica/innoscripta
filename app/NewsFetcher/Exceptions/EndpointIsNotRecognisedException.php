<?php

namespace App\NewsFetcher\Exceptions;

use Exception;

class EndpointIsNotRecognisedException extends Exception
{
    public const EXCEPTION_CODE = 1001;

    public function __construct(string $endpointName, string $fetcherClass)
    {
        parent::__construct(
            "The endpoint '$endpointName' is not recognised for '$fetcherClass'.",
            self::EXCEPTION_CODE
        );
    }
}
