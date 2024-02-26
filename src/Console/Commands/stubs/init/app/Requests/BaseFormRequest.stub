<?php

namespace App\Http\Requests;

use App\Traits\FailedAuthorization;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    use FailedAuthorization, FailedValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
