<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Contracts\UserRepositoryInterface;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\Response;
use App\Events\ForgotPassword;

class ForgotPasswordService
{

    public function __construct(private UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle forgot password.
     *
     * @param  \App\Http\Requests\Auth\ForgotPasswordRequest  $request
     * @return \App\Actions\ResponseData
     */
    public function handleForgotPassword(ForgotPasswordRequest $request): ResponseData
    {
        $validated = $request->validated();

        if ($user = $this->userRepository->getByEmail($validated['email'])
                        ->first()
        ) {
            ForgotPassword::dispatch($user);
        }

        return responseData(true, Response::HTTP_OK,
                trans('passwords.sent'));
    }
}
