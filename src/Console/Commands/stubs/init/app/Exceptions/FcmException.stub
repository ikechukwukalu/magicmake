<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FcmException extends Exception
{
    public function render(): JsonResponse
    {
        $response = responseData(false, $this->getCode(), $this->getMessage());
        return httpJsonResponse($response);
    }
}
