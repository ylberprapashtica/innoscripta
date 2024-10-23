<?php

namespace App\NewsFetcher\Exceptions;

use Exception;
use Illuminate\Validation\Validator;

class ParametersValidationFailException extends Exception
{
    public const EXCEPTION_CODE = 1004;

    public Validator $validator;

    public function __construct(Validator $validator, string $fetcherClass)
    {
        $this->validator = $validator;

        parent::__construct(
            "The api call could not be made because parameter validation failed for '$fetcherClass'." . print_r($validator->errors()->getMessages(), true),
            self::EXCEPTION_CODE
        );
    }
}
