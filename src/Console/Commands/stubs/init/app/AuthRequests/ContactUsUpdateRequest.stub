<?php

namespace App\Http\Requests\Auth;

use App\Enums\ContactType;
use App\Http\Requests\BaseFormRequest;

class ContactUsUpdateRequest extends BaseFormRequest
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
            'id' => ['required', 'exists:contact_us,id'],
            'message' => ['string', 'nullable'],
            'reason' => ['string', 'nullable'],
            'type' => 'required|in:' . implode(',', ContactType::toArray()),
        ];
    }
}
