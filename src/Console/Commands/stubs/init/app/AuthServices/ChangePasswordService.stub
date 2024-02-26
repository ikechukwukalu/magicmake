<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Contracts\OldPasswordRepositoryInterface;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\PasswordChange;

class ChangePasswordService
{

    public function __construct(private OldPasswordRepositoryInterface $oldPasswordRepository) {
        $this->oldPasswordRepository = $oldPasswordRepository;
    }

    /**
     * Change password
     *
     * @param ChangePasswordRequest $request
     * @return ResponseData
     */
    public function handlePasswordChange(ChangePasswordRequest $request): ResponseData
    {
        $validated = $request->validated();
        $user = Auth::user();
        $user->password = Hash::make($validated['password']);

        if ($user->save()) {
            $this->oldPasswordRepository->create([
                'user_id' => $user->id,
                'password' => Hash::make($validated['password'])
            ]);

            if (config('api.password.notify.change', true)) {
                $user->notify(new PasswordChange());
            }

            return responseData(true, Response::HTTP_OK,
                    trans('passwords.changed'));
        }

        return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                trans('general.unknown_error'));
    }

}
