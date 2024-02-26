<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{

    public function __construct(private RegisterService $registerService)
    {
        $this->middleware('guest');
    }

    /**
     * User form registration.
     *
     * Within the config file, you are required to determine whether a
     * user should recieve welcome and verification emails after
     * registration by setting <b>registration.notify.welcome</b> to <b>TRUE</b> and
     * <b>registration.notify.verify</b> to <b>TRUE</b> respectively.
     * You can also set <b>registration.autologin</b> to <b>TRUE</b>.
     *
     * @bodyParam name string required The full name of the user. Example: John Bison Doe
     * @bodyParam first_name string required The first name of the user. Example: John
     * @bodyParam middle_name string required The middle name of the user. Example: Bison
     * @bodyParam last_name string required The last name of the user. Example: Doe
     * @bodyParam email string required The email of the user. Example: johndoe@xyz.com
     * @bodyParam phone string required The phone of the user. Example: 08012345678
     * @bodyParam unit_number string required The unit number of the user. Example: NG08012345678
     * @bodyParam password string required The password for user authentication must contain uppercase, lowercase, symbols, numbers. Example: Ex@m122p$%l6E
     * @bodyParam password_confirmation string required Must match <small class="badge badge-blue">password</small> Field. Example: Ex@m122p$%l6E
     * @bodyParam type string The type of user must either be individual or business. Example: individual
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
     * @group No Auth APIs
     */
    protected function register(RegisterRequest $request): JsonResponse
    {
        if ($data = $this->registerService->handleRegistration($request)) {
            return httpJsonResponse($data);
        }

        return unknownErrorJsonResponse();
    }
}
