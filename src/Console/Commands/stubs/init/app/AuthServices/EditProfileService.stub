<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Http\Requests\Auth\EditProfileRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EditProfileService
{
    /**
     * Handle edit profile.
     *
     * @param  \App\Http\Requests\Auth\EditProfileRequest  $request
     * @return \App\Actions\ResponseData
     */
    public function handleEditProfile(EditProfileRequest $request): ResponseData
    {
        $validated = $request->validated();
        $user = Auth::user();
        $user->fill($validated);

        if (empty($user->getDirty())) {
            return responseData(false, Response::HTTP_ACCEPTED,
                    trans('general.no_changes'));
        }

        if ($user->save()) {
            return responseData(true, Response::HTTP_OK,
                    trans('profile.changed'));
        }

        return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                trans('general.unknown_error'));
    }
}
