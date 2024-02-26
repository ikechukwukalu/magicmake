<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Services\Auth\ForgotPasswordService;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{

    public function __construct(private ForgotPasswordService $forgotPasswordService)
    {
        $this->middleware('guest');
    }


    /**
     * User forgot password.
     *
     * The user must enter a registered email.
     *
     * @bodyParam email string required The email of the user. Example: johndoe@xyz.com
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
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        if ($data = $this->forgotPasswordService->handleForgotPassword($request)) {
            return httpJsonResponse($data);
        }

        return unknownErrorJsonResponse();
    }
}
