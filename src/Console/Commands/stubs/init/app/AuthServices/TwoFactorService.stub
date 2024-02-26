<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Http\Requests\Auth\ConFirmTwoFactorRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TwoFactorService
{

    /**
     * Handle confirm 2FA.
     *
     * @param  \App\Http\Requests\Auth\ConFirmTwoFactorRequest  $request
     * @return null|array
     */
    public function handleConFirmTwoFactor(ConFirmTwoFactorRequest $request): ResponseData
    {
        $validated = $request->validated();

        if (!$request->user()->confirmTwoFactorAuth($validated['code']))
        {
            return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('general.unknown_error'));
        }

        DB::table('two_factor_authentications')
            ->where('authenticatable_id', $request->user()->id)
            ->update(['authenticatable_type' => \App\Models\User::class]);

        Auth::user()->update([
            'two_factor' => '1'
        ]);

        return $this->generateRecoveryCodes($request);
    }

    /**
     * Create 2FA
     *
     * @param Request $request
     * @return ResponseData
     */
    public function handleCreateTwoFactor(Request $request): ResponseData
    {
        if ($secret = $request->user()->createTwoFactorAuth()) {
            return responseData(true, Response::HTTP_OK,
                    trans('crud.created'),
                    [
                        'qr_code' => $secret->toQr(),     // As QR Code
                        'uri'     => $secret->toUri(),    // As "otpauth://" URI.
                        'string'  => $secret->toString(), // As a string
                    ]);
        }

        return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                trans('general.unknown_error'));
    }

    /**
     * Disable 2FA
     *
     * @param Request $request
     * @return ResponseData
     */
    public function handleDisableTwoFactor(Request $request): ResponseData
    {
        $request->user()->disableTwoFactorAuth();

        Auth::user()->update([
            'two_factor' => '0'
        ]);

        return responseData(true, Response::HTTP_OK,
                trans('auth.disabled_2fa'));
    }

    /**
     * Retrieve recovery codes
     *
     * @param Request $request
     * @return ResponseData
     */
    public function handleCurrentRecoveryCodes(Request $request): ResponseData
    {
        return responseData(true, Response::HTTP_OK,
                trans('auth.download_code'),
                $request->user()->getRecoveryCodes());
    }

    public function handleNewRecoveryCodes(Request $request): ResponseData
    {
        return $this->generateRecoveryCodes($request);
    }

    public function isTwoFactorEnabled(): bool
    {
        return Auth::user()->two_factor;
    }

    private function generateRecoveryCodes(Request $request): ResponseData
    {
        return responseData(true, Response::HTTP_OK,
                trans('auth.download_code'),
                $request->user()->generateRecoveryCodes());
    }

}
