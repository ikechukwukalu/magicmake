<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

trait FailedValidation
{

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $message = $errors[array_key_first($errors)][0];
        $data = responseData(false, Response::HTTP_FAILED_DEPENDENCY, $message, $errors);

        throw new HttpResponseException(
            httpJsonResponse($data)
        );
    }

}
