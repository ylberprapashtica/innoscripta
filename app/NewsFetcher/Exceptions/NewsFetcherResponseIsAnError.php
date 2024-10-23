<?php

namespace App\NewsFetcher\Exceptions;

use Exception;

class NewsFetcherResponseIsAnError extends Exception
{
    public const EXCEPTION_CODE = 1003;

    public function __construct(string $errorCode, string $fetcherClass)
    {
        parent::__construct(
            "Responded with the error code '$errorCode', for '$fetcherClass'.",
            self::EXCEPTION_CODE
        );
    }
}
