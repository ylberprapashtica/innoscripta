<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;

class ApiResponse extends JsonResponse
{
    public function __construct($message = "Ok", $data = null, $status = 200, $headers = [], $options = 0, $json = false)
    {

        parent::__construct([
            'message' => $message,
            'data' => $data
        ], $status, $headers, $options, $json);
    }
}
