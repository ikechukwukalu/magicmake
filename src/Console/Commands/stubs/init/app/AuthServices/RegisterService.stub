<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Contracts\UserRepositoryInterface;
use App\Events\EmailVerification;
use App\Events\PhoneVerification;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\WelcomeUser;
use Illuminate\http\Response;
use Illuminate\Support\Facades\Hash;

class RegisterService
{

    public function __construct(private UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the request.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return \App\Actions\ResponseData
     */
    public function handleRegistration(RegisterRequest $request): ResponseData
    {
        $validated = $request->validated();
        $user =  User::create([
            'email' => $validated['email'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'model' => \App\Models\Customer::class,
            'password' => Hash::make($validated['password']),
            'form_signup' => true,
        ]);

        if (!isset($user->id)) {
            return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('general.unknown_error'));
        }

        $user->refresh();

        $data = [];
        if (config('api.registration.autologin', false)) {
            $token = $user->createToken($validated['email']);
            $data['access_token'] = $token->plainTextToken;
        }

        if (config('api.registration.notify.welcome', true)) {
            $user->notify(new WelcomeUser($user));
        }

        if (config('api.registration.notify.verify', true)) {
            EmailVerification::dispatch($user);

            $otp = rand(10000, 99999);

            $this->userRepository->update($user->id, [
                'otp' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            if (!app()->isProduction()) {
                appLog("$user->phone $otp");
            }

            PhoneVerification::dispatch($user, $otp);
        }

       return responseData(true, Response::HTTP_OK,
                trans('register.success'), $data);
    }
}
