<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Contracts\SocialiteLoginRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\UserPasswordHolderRepositoryInterface;
use App\Models\Customer;
use App\Models\User;
use App\Events\SocialiteLogin as SocialiteLoginEvent;
use App\Models\UserPasswordHolder;
use App\Traits\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Laragear\TwoFactor\Facades\Auth2FA;
use Laravel\Socialite\Facades\Socialite;

class SocialiteService
{
    use Helpers;

    private SocialiteLoginRepositoryInterface $socialiteLoginRepository;
    private UserRepositoryInterface $userRepository;
    private UserPasswordHolderRepositoryInterface $userPasswordHolderRepository;

    public function __construct(SocialiteLoginRepositoryInterface $socialiteLoginRepository,
        UserRepositoryInterface $userRepository,
        UserPasswordHolderRepositoryInterface $userPasswordHolderRepository)
    {
        $this->socialiteLoginRepository = $socialiteLoginRepository;
        $this->userRepository = $userRepository;
        $this->userPasswordHolderRepository = $userPasswordHolderRepository;
    }

    public function handleAuthRedirect(Request $request): void
    {
       $this->socialiteLoginRepository->firstOrCreate(
            [
                'user_uuid' => $request->uuid,
                'used' => false,
            ],
            [
                'user_uuid' => $request->uuid,
                'ip_address' => $this->getUserIp($request),
                'user_agent' => $request->userAgent(),
            ]
        );

        session(['user_uuid' => $request->uuid]);
    }

    public function handleAuthCallback(Request $request): null|User
    {
        $userUUID = session('user_uuid');
        if (!$userUUID) {
            abort(440, trans('cookie.error_440'));
        }

        $user = $this->getUserDetails();
        $userPasswordHolder = $this->holdUserPassword($user);
        $tempPassword = $this->replaceUserPassword($user);

        if (!Auth2FA::attempt([
                'email' => $user->email,
                'password' => $tempPassword
            ], true))
        {
                $this->normaliseUserPassword($user, $userPasswordHolder);
                $this->forgetSessions();
                return null;
        }

        $this->throttleRequestsService->clearAttempts($request);
        $this->normaliseUserPassword($user, $userPasswordHolder);
        $this->forgetSessions();
        $this->userLoginNotification($user);

        SocialiteLoginEvent::dispatch($user, $user->createToken($user->email),
            $userUUID);
        $socialiteLogin = $this->socialiteLoginRepository->getByUserUuid($userUUID);

        $this->socialiteLoginRepository->update($socialiteLogin->id, [
            'user_id' => $user->id,
            'email' => $user->email,
            'used' => true
        ]);

        return $user;
    }

    public function loginRequestAttempts(Request $request): null|ResponseData
    {
        return $this->requestAttempts($request, 'auth.throttle');
    }

    private function getUserDetails(): User
    {
        if (!session('user')) {
            $google = Socialite::driver('google')->user();
            $user = $this->userRepository->firstOrCreate(
                [
                    'email' => $google->email,
                ],
                [
                    'name' => $google->name,
                    'email' => $google->email,
                    'socialite_signup' => true,
                    'password' => Hash::make($google->email),
                    'fake_password' => Crypt::encryptString($google->email),
                    'model' => Customer::class,
                    'type' => 'individual',
                    'account_owner' => true,
                    'email_verified_at' => now(),
                ]);

            session(['user' => $user]);

            return $user;
        }

        return session('user');
    }

    private function holdUserPassword(User $user): UserPasswordHolder
    {
        $userPasswordHolder = $this->userPasswordHolderRepository
                                ->belongsToUser($user);

        if (!isset($userPasswordHolder->user_id)) {
            session(['holds_user_password' => true]);

            return $this->userPasswordHolderRepository->create([
                    'user_id' => $user->id,
                    'password' => $user->password
            ]);
        }

        if (!session('holds_user_password')) {
            $this->userPasswordHolderRepository->update($userPasswordHolder->id, [
                'password' => $user->password
            ]);

            session(['holds_user_password' => true]);
        }

        return $userPasswordHolder;
    }

    private function replaceUserPassword(User $user): string
    {
        $tempPassword = $this->generateSalt();

        $this->userRepository->update($user->id, [
            'password' => Hash::make($tempPassword)
        ]);

        return $tempPassword;
    }

    private function normaliseUserPassword(User $user, UserPasswordHolder $userPasswordHolder): void
    {
        $this->userRepository->update($user->id, [
            'password' => $userPasswordHolder->password
        ]);

        $this->userPasswordHolderRepository->update($userPasswordHolder->id, [
            'password' => $userPasswordHolder->password
        ]);

        session()->forget('holds_user_password');
    }

    private function forgetSessions(): void
    {
        session()->forget('user_uuid');
        session()->forget('user');
    }
}
