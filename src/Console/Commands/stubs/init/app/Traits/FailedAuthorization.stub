<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

trait FailedAuthorization
{

    protected function failedAuthorization()
    {
        $errors = null;
        $message = "This action is unauthorized.";
        $data = responseData(false, Response::HTTP_UNPROCESSABLE_ENTITY, $message, $errors);

        throw new HttpResponseException(
            httpJsonResponse($data)
        );
    }
}
