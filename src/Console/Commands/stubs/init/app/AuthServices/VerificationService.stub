<?php

namespace App\Services\Auth;

use App\Contracts\UserRepositoryInterface;
use App\Events\PhoneVerification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Events\EmailVerification;

class VerificationService
{

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Verify user email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleVerifyUserEmail(Request $request): JsonResponse
    {
        if (!$request->hasValidSignature()) {
            return httpJsonResponse(responseData(false, Response::HTTP_BAD_REQUEST,
                    trans('verify.url_invalid')));
        }

        if (!$request->id) {
            return httpJsonResponse(responseData(false, Response::HTTP_FORBIDDEN,
                    trans('verify.url_invalid')));
        }

        if (!$user = $this->userRepository->getById($request->id)) {
            return httpJsonResponse(responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('user.not_found')));
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return httpJsonResponse(responseData(true, Response::HTTP_OK,
                trans('verify.verified')));
    }

    /**
     * Resend email verification
     *
     * @return JsonResponse
     */
    public function resendUserEmailVerification(): JsonResponse
    {
        if (!$user = Auth::user()) {
            return httpJsonResponse(responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('user.not_found')));
        }

        if ($user->hasVerifiedEmail()) {
            return httpJsonResponse(responseData(true, Response::HTTP_ACCEPTED,
                    trans('verify.already_verified')));
        }

        EmailVerification::dispatch($user);

        return httpJsonResponse(responseData(true, Response::HTTP_OK,
                trans('verify.sent')));
    }

    /**
     * verify user's phone
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleVerifyUserPhone(Request $request): JsonResponse
    {
        if (!$request->id) {
            return httpJsonResponse(responseData(false, Response::HTTP_FORBIDDEN,
                    trans('verify.url_invalid')));
        }

        if (!$user = $this->userRepository->getById($request->id)) {
            return httpJsonResponse(responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('user.not_found')));
        }

        if (!$user->hasVerifiedPhone()) {
            $user->markPhoneAsVerified();
        }

        return httpJsonResponse(responseData(true, Response::HTTP_OK,
                trans('verify.verified')));
    }

    /**
     * Resend phone verification
     *
     * @return JsonResponse
     */
    public function resendUserPhoneVerification(): JsonResponse
    {
        if (!$user = Auth::user()) {
            return httpJsonResponse(responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('user.not_found')));
        }

        if ($user->hasVerifiedPhone()) {
            return httpJsonResponse(responseData(true, Response::HTTP_ACCEPTED,
                    trans('verify.already_verified')));
        }

        PhoneVerification::dispatch($user, rand(1000, 9999));

        return httpJsonResponse(responseData(true, Response::HTTP_OK,
                trans('verify.sent_otp')));
    }
}
