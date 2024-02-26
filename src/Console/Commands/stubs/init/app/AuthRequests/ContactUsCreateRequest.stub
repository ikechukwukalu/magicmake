<?php

namespace App\Http\Requests\Auth;

use App\Enums\ContactType;
use App\Http\Requests\BaseFormRequest;

class ContactUsCreateRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => 'required|string|max:150',
            'subject' => 'required|string|max:150',
            'message' => 'required|string',
            'type' => 'required|in:' . implode(',', ContactType::toArray()),
        ];
    }
}
