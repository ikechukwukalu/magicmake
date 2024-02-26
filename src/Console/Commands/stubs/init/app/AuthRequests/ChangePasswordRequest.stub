<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use App\Rules\CurrentPassword;
use App\Rules\DisallowOldPassword;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends BaseFormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', new CurrentPassword],
            'password' => ['required', 'string', 'max:16',
                Password::min(8)
                    ->letters()->mixedCase()
                    ->numbers()->symbols()
                    ->uncompromised(),
                'confirmed',
                new DisallowOldPassword(
                    config('api.password.check_all', true),
                    config('api.password.number', 4)
                )
            ],
        ];
    }

}
