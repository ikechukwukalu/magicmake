<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{

    /**
     * User logout.
     *
     * This API logs a user out of a single session
     *
     * @header Authorization Bearer {Your key}
     *
     * @response 200
     *
     * {
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
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return httpJsonResponse(responseData(true, Response::HTTP_OK,
                trans('auth.logout')));
    }

    /**
     * User logout from all sessions.
     *
     * This API logs a user out of every session and clears all user tokens
     *
     * @header Authorization Bearer {Your key}
     *
     * @response 200
     *
     * {
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
    public function logoutFromAllSessions(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return httpJsonResponse(responseData(true, Response::HTTP_OK,
                trans('auth.logout')));
    }
}
