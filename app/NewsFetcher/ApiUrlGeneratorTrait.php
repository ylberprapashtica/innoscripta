<?php

namespace App\NewsFetcher;

use App\NewsFetcher\Exceptions\ApiUrlIsNotValidUrlException;
use App\NewsFetcher\Exceptions\EndpointIsNotRecognisedException;
use App\NewsFetcher\Exceptions\ParametersValidationFailException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ApiUrlGeneratorTrait
{
    /**
     * @throws EndpointIsNotRecognisedException
     * @throws ApiUrlIsNotValidUrlException
     * @throws ParametersValidationFailException
     * @throws ValidationException
     */
    protected function createQuery(string $endpoint, array $parameters): string
    {
        $validator = Validator::make([
            'apiUrl' => $this->getApiUrl(),
            'endpoint' => $endpoint
        ], [
            'apiUrl' => 'required|url',
            'endpoint' => 'required|string|in:' . implode(',', array_keys($this->getValidationRulesPerEndpoint()))
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('apiUrl')) {
                throw new ApiUrlIsNotValidUrlException($this->getApiUrl(), self::class);
            }
            if ($validator->errors()->has('endpoint')) {
                throw new EndpointIsNotRecognisedException($endpoint, self::class);
            }
        }

        $validator = Validator::make($parameters, $this->getValidationRulesPerEndpoint()[$endpoint]);

        if ($validator->fails()) {
            throw new ParametersValidationFailException($validator, self::class);
        }

        return $this->getApiUrl() . $endpoint . '?' . http_build_query($validator->validated());
    }

    abstract function getApiUrl(): string;

    /**
     * Keys of the array are the endpoints of the API.
     * The value is an array of validation rules for that endpoints parameters
     * @return array
     */
    abstract function getValidationRulesPerEndpoint(): array;
}
