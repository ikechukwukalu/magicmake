<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EditProfileRequest;
use App\Services\Auth\EditProfileService;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{

    public function __construct(private EditProfileService $editProfileService)
    { }

    /**
     * User edit profile.
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam name string required The full name of the user. Example: John Bison Doe
     * @bodyParam first_name string required The first name of the user. Example: John
     * @bodyParam middle_name string required The middle name of the user. Example: Bison
     * @bodyParam last_name string required The last name of the user. Example: Doe
     * @bodyParam email string required The email of the user. Example: johndoe@xyz.com
     * @bodyParam phone string required The phone of the user. Example: 08012345678
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

    public function editProfile(EditProfileRequest $request): JsonResponse
    {
        if ($data = $this->editProfileService->handleEditProfile($request)) {
            return httpJsonResponse($data);
        }

        return unknownErrorJsonResponse();
    }
}
