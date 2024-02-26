<?php

namespace App\Exceptions;

use App\Actions\ResponseData;
use Exception;
use Illuminate\Http\JsonResponse;

class CustomException extends Exception
{

    public function __construct(private ResponseData $response)
    {
        parent::__construct(null);
    }

    public function render(): JsonResponse
    {
        return httpJsonResponse($this->response);
    }
}
