<?php

namespace App\NewsFetcher\Exceptions;

use Exception;

class ApiUrlIsNotValidUrlException extends Exception
{
    public const EXCEPTION_CODE = 1000;

    public function __construct(string $apiUrl, string $fetcherClass)
    {
        parent::__construct(
            "The api url '$apiUrl' is not a valid Url for '$fetcherClass'.",
            self::EXCEPTION_CODE
        );
    }
}
