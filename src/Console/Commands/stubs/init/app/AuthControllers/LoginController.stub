<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends Controller
{

    public function __construct(private LoginService $loginService)
    { }

    /**
     * User form login.
     *
     * You can choose to notify a User whenever there has been a Login by setting
     * <b>password.notify.change</b> to <b>TRUE</b> Within the config file,
     *
     * Make sure to retrieve <small class="badge badge-blue">access_token</small> after login for User authentication
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam email string required The email of the user. Example: johndoe@xyz.com
     * @bodyParam password string required The password for user authentication must contain uppercase, lowercase, symbols, numbers. Example: Ex@m122p$%l6E
     * @bodyParam remember_me int Could be set to 0 or 1. Example: 1
     *
     * @response 200 {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "message": string
     *      "access_token": string
     *  }
     * }
     *
     * @group No Auth APIs
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if ($data = $this->loginService->loginRequestAttempts($request)) {
            return httpJsonResponse($data);
        }

        if ($data = $this->loginService->handleLogin($request)) {
            return httpJsonResponse($data);
        }

        return httpJsonResponse(responseData(false,
            Response::HTTP_BAD_REQUEST,
            trans('auth.failed')));
    }

    public function twoFactorLogin(Request $request)
    {
        if ($data = $this->loginService->loginRequestAttempts($request)) {
            return $this->loginService->returnTwoFactorLoginView($data->data);
        }

        if ($data = $this->loginService->twoFactorLoginUrlHasValidUUID($request->uuid)) {
            return $this->loginService->returnTwoFactorLoginView($data->data);
        }

        if ($data = $this->loginService->handleTwoFactorLogin($request)) {
            return view('twofactor.callback', $data->data);
        }

        return $this->loginService->returnTwoFactorLoginView();
    }
}
