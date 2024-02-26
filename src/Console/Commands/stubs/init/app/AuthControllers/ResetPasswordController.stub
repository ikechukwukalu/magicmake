<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\ResetPasswordService;
use Illuminate\Http\JsonResponse;
use App\Traits\Helpers;

class ResetPasswordController extends Controller
{
    use Helpers;

    private ResetPasswordService $resetPasswordService;

    public function __construct(ResetPasswordService $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * User password reset.
     *
     * @bodyParam email string required The email of the user. Example: johndoe@xyz.com
     * @bodyParam password string required The password for user authentication must contain uppercase, lowercase, symbols, numbers. Example: Ex@m122p$%l6E
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
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        if ($data = $this->resetPasswordService->handleResetPassword($request)) {
            return httpJsonResponse($data);
        }

        return unknownErrorJsonResponse();
    }

    /**
     * Password reset form.
     *
     * Presents a form were a user must input the generated code.
     *
     * @header Accept text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*\/*;q=0.8,application/signed-exchange;v=b3;q=0.9
     * @header Content-Type text/html; charset=UTF-8
     *
     * @bodyParam email string required The email of the user. Example: johndoe@xyz.com
     * @bodyParam password string required The password for user authentication must contain uppercase, lowercase, symbols, numbers. Example: Ex@m122p$%l6E
     *
     * @response 200
     *
     * @group Web URLs
     */
    public function resetPasswordForm(ResetPasswordRequest $request)
    {
        $data = $this->resetPasswordService->handleResetPasswordForm($request);
        $status = $data->success ? "Success" : "Fail";

        session()->flash($status, $data->message);

        return redirect()->route('password.reset');
    }

}
