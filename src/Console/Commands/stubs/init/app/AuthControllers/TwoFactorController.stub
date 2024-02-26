<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ConFirmTwoFactorRequest;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TwoFactorController extends Controller
{

    public function __construct(private TwoFactorService $twoFactorService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Create 2FA.
     *
     * To enable Two-Factor Authentication for the User,
     * he/she must sync the Shared Secret between its Authenticator
     * app and the application.
     *
     * @header Authorization Bearer {Your key}
     *
     * @response 200
     *
     * {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "qr_code": string,
     *      "uri": string,
     *      "string": string,
     *  }
     * }
     *
     * @authenticated
     * @subgroup User APIs
     * @group Auth APIs
     */
    public function createTwoFactor(Request $request): JsonResponse
    {
        if ($data = $this->twoFactorService->handleCreateTwoFactor($request)) {
            return httpJsonResponse($data);
        }

        return unknownErrorJsonResponse();
    }

    /**
     * Confirm 2FA.
     *
     * Recovery codes will be generated if code is invalid.
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam code numeric required The authenticator code. Example: 123 456
     *
     * @response 200
     *
     * {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "message": string,
     *      "codes": array,
     *  }
     * }
     *
     * @authenticated
     * @subgroup User APIs
     * @group Auth APIs
     */
    public function confirmTwoFactor(ConFirmTwoFactorRequest $request): JsonResponse
    {
        if ($this->twoFactorService->isTwoFactorEnabled()) {
            return httpJsonResponse(responseData(false, Response::HTTP_BAD_REQUEST,
                    trans('auth.2fa_is_enabled')));
        }

        if ($data = $this->twoFactorService->handleConFirmTwoFactor($request))
        {
            return httpJsonResponse($data);
        }

        return unknownErrorJsonResponse();

    }

    /**
     * Disable 2FA.
     *
     * To disable Two-Factor Authentication for the User.
     *
     * @header Authorization Bearer {Your key}
     *
     * @response 200
     *
     * {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "message": string,
     *  }
     * }
     *
     * @authenticated
     * @subgroup User APIs
     * @group Auth APIs
     */
    public function disableTwoFactor(Request $request): JsonResponse
    {
        if ($data = $this->twoFactorService->handleDisableTwoFactor($request))
        {
            return httpJsonResponse($data);
        }

        return unknownErrorJsonResponse();
    }

    /**
     * Get 2FA recovery codes.
     *
     * The User can retrieve current recovery codes.
     *
     * @header Authorization Bearer {Your key}
     *
     * @response 200
     *
     * {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "message": string,
     *      "codes": array,
     *  }
     * }
     *
     * @authenticated
     * @subgroup User APIs
     * @group Auth APIs
     */
    public function currentRecoveryCodes(Request $request)
    {
        if ($data = $this->twoFactorService->handleCurrentRecoveryCodes($request))
        {
            return httpJsonResponse($data);
        }

        return unknownErrorJsonResponse();
    }

    /**
     * New 2FA recovery codes.
     *
     * The User can generate a fresh batch of codes which replaces
     * the previous batch.
     *
     * @header Authorization Bearer {Your key}
     *
     * @response 200
     *
     * {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "message": string,
     *      "codes": array,
     *  }
     * }
     *
     * @authenticated
     * @subgroup User APIs
     * @group Auth APIs
     */
    public function newRecoveryCodes(Request $request)
    {
        if ($data = $this->twoFactorService->handleNewRecoveryCodes($request))
        {
            return httpJsonResponse($data);
        }

        return unknownErrorJsonResponse();
    }
}
