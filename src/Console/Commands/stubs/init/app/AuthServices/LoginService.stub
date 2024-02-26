<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Contracts\CustomerRepositoryInterface;
use App\Contracts\TwoFactorLoginRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\Auth\ThrottleRequestsService;
use App\Events\TwoFactorLogin as TwoFactorLoginEvent;
use App\Models\TwoFactorLogin;
use App\Traits\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Laragear\TwoFactor\Facades\Auth2FA;

class LoginService {

    use Helpers;

    private CustomerRepositoryInterface $customerRepository;
    private TwoFactorLoginRepositoryInterface $twoFactorLoginRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository,
        ThrottleRequestsService $throttleRequestsService,
        TwoFactorLoginRepositoryInterface $twoFactorLoginRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->throttleRequestsService = $throttleRequestsService;
        $this->twoFactorLoginRepository = $twoFactorLoginRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Log user in
     *
     * @param LoginRequest $request
     * @return null|ResponseData
     */
    public function handleLogin(LoginRequest $request): null|ResponseData
    {
        $validated = $request->validated();
        $remember = isset($validated['remember_me']) ? true : false;

        if (!Auth::attempt([
                'email' => $validated['email'],
                'password' => $validated['password']
            ], $remember))
        {
            return null;
        }

        $this->throttleRequestsService->clearAttempts($request);

        $user = Auth::user();

        if ($user->two_factor) {
            return $this->generateTwoFactorURL($request, $validated, $user);
        }

        return $this->finaliseLoginProcess($user, $validated);
    }

    /**
     * Login for 2FA user
     *
     * @param Request $request
     * @return null|ResponseData
     */
    public function handleTwoFactorLogin(Request $request): null|ResponseData
    {
        $tfa_pass = $this->getTwoFactorLoginPassword($request->uuid);

        if (!isset($tfa_pass->id)) {
            return null;
        }

        $email = $request->email;
        $salt = Crypt::decryptString($tfa_pass->salt);
        $password = str_replace($salt, "", Crypt::decryptString(
                        $tfa_pass->password));
        $validated = [
            'email' => $email,
            'password' => $password
        ];

        if (!Auth2FA::attempt($validated, true))
        {
            return null;
        }

        $this->throttleRequestsService->clearAttempts($request);

        $tfa_pass->update([
            'used' => true,
            'password' => null,
            'salt' => null
        ]);

        $user = Auth::user();
        $validated['uuid'] = $tfa_pass->user_uuid;

        return $this->finaliseLoginProcess($user, $validated, true);
    }

    /**
     * Login throttling
     *
     * @param Request $request
     * @return null|ResponseData
     */
    public function loginRequestAttempts(Request $request): null|ResponseData
    {
        if ($data = $this->requestAttempts($request, 'auth.throttle')) {
            return responseData(false, Response::HTTP_BAD_REQUEST,
                        $data['message']);
        }

        return null;
    }

    /**
     * Check if 2FA is valid
     *
     * @param string $uuid
     * @return null|ResponseData
     */
    public function twoFactorLoginUrlHasValidUUID(string $uuid): null|ResponseData
    {
        if(!$this->getTwoFactorLoginPassword($uuid)) {
            return responseData(false, Response::HTTP_BAD_REQUEST,
                trans('auth.invalid_url'));
        }

        return null;
    }

    /**
     * Generate 2FA URL
     *
     * @param Request $request
     * @param array $validated
     * @param User $user
     * @return ResponseData
     */
    private function generateTwoFactorURL(Request $request, array $validated, User $user): ResponseData
    {
        $salt = $this->generateSalt();
        $cryptedPassword = Crypt::encryptString($validated['password'] . $salt);
        $uuid = (string) Str::uuid();

        TwoFactorLogin::firstOrCreate(
            [
                'user_uuid' => $uuid,
                'email' => $validated['email'],
                'used' => false,
            ],
            [
                'user_id' => $user->id,
                'user_uuid' => $uuid,
                'email' => $validated['email'],
                'password' => $cryptedPassword,
                'salt' => Crypt::encryptString($salt),
                'ip_address' => $this->getUserIp($request),
                'user_agent' => $request->userAgent(),
                'type' => 'twofactor',
            ]);

        $twofactor_url = route('twofactor.required', ['uuid' => $uuid,
                            'email' => $validated['email']]);

        Auth::logout();

        return responseData(true, Response::HTTP_OK,
            trans('auth.enter_2fa'), [
                'twofactor_url' => $twofactor_url,
                'user_uuid' => $uuid,
                'message' => trans('auth.enter_2fa'),
            ]);
    }

    /**
     * Final login process
     *
     * @param mixed $user
     * @param array $validated
     * @param boolean $twoFactor
     * @return ResponseData
     */
    private function finaliseLoginProcess(mixed $user, array $validated,
                bool $twoFactor = false): ResponseData
    {
        $token = $user->createToken($validated['email']);
        $this->userLoginNotification($user);

        if ($twoFactor) {
            TwoFactorLoginEvent::dispatch($user, $token, $validated['uuid']);

            $data = [
                'user' => $user,
                'access_token' => null
            ];

            return responseData(true, Response::HTTP_OK,
                trans('auth.success'), $data);
        }

        $data = [
            'user' => $user,
            'access_token' => $token->plainTextToken
        ];

        return responseData(true, Response::HTTP_OK,
            trans('auth.success'), $data);
    }

    /**
     * Get 2FA login password
     *
     * @param string $uuid
     * @param boolean $used
     * @return TwoFactorLogin|null
     */
    private function getTwoFactorLoginPassword(
        string $uuid,
        bool $used = false): null|TwoFactorLogin
    {
        return TwoFactorLogin::where('user_uuid', $uuid)
                    ->where('used', $used)->first();
    }
}
