<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Contracts\UserRepositoryInterface;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ResetPasswordService
{

    public function __construct(private UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle reset password.
     *
     * @param  \App\Http\Requests\Auth\ResetPasswordRequest  $request
     * @return null|array
     */
    public function handleResetPassword(ResetPasswordRequest $request): ResponseData
    {
        $validated = $request->validated();

        if ($user = $this->userRepository->getByEmail($validated['email'])
                        ->first()
        ) {
            $this->userRepository->update($user->id,
                ['password' => hash::make($validated['password'])]);
        }

        return responseData(true, Response::HTTP_OK,
                trans('passwords.reset'));
    }

    /**
     * Reset password form
     *
     * @param ResetPasswordRequest $request
     * @return ResponseData
     */
    public function handleResetPasswordForm(ResetPasswordRequest $request): ResponseData
    {
        $responseData = $this->handleResetPassword($request);
        return $responseData;
    }

}
