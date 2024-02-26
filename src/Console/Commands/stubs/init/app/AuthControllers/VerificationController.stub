<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\VerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{

    public function __construct(private VerificationService $verificationService)
    { }

    /**
     * User email verification.
     *
     * This endpoint must have a valid laravel generated URL signature to work.
     * It is automatically sent after a successful registration and
     * <b>registration.notify.verify</b> is set to <b>TRUE</b> within
     * the config file.
     *
     * @urlParam id string required
     * <small class="badge badge-blue">id</small>
     * Field must belong to a registered User. Example: 1
     *
     * @response 200 {
     * "success": true,
     * "status_code": 200,
     * "message": string
     * "data": {}
     * }
     *
     * @group No Auth APIs
     */
    public function verifyUserEmail(Request $request): JsonResponse
    {
        return $this->verificationService->handleVerifyUserEmail($request);
    }

    /**
     * User resend email verification.
     *
     * This endpoint is used to generate and send via email a URL for User email verification to a registered User.
     *
     * @header Authorization Bearer {Your key}
     *
     * @queryParam id string required <small class="badge badge-blue">id</small> Field must belong to a registered User. Example: 1
     *
     * @response 200 {
     * "success": true,
     * "status_code": 200,
     * "message": string
     * "data": {}
     * }
     *
     * @authenticated
     * @subgroup User APIs
     * @group Auth APIs
     */
    public function resendUserEmailVerification(Request $reques): JsonResponse
    {
        return $this->verificationService->resendUserEmailVerification();
    }
}
