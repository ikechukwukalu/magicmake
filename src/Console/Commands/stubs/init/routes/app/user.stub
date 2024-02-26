<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\ContactUsController;
use App\Http\Controllers\Auth\UserDeviceTokenController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {

    Route::post('resend/verify/email', [VerificationController::class, 'resendUserEmailVerification'])->name('verification.resend');

    Route::middleware(['check.email.verification'])->group(function () {

        Route::post('change/password', [ChangePasswordController::class, 'changePassword'])->name('changePassword');
        Route::post('edit/profile', [ProfileController::class, 'editProfile'])->name('editProfile');
        Route::post('create-two-factor', [TwoFactorController::class, 'createTwoFactor'])->name('createTwoFactor');
        Route::post('confirm-two-factor', [TwoFactorController::class, 'confirmTwoFactor'])->name('confirmTwoFactor');
        Route::post('disable-two-factor', [TwoFactorController::class, 'disableTwoFactor'])->name('disableTwoFactor');
        Route::post('current-recovery-codes', [TwoFactorController::class, 'currentRecoveryCodes'])->name('currentRecoveryCodes');
        Route::post('new-recovery-codes', [TwoFactorController::class, 'newRecoveryCodes'])->name('newRecoveryCodes');
        Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
        Route::post('logout-from-all-sessions', [LogoutController::class, 'logoutFromAllSessions'])->name('logoutFromAllSessions');
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::prefix('device_token')->group(function () {
            Route::post('create', [UserDeviceTokenController::class, 'create'])->name('createUserDeviceToken');
            Route::delete('delete', [UserDeviceTokenController::class, 'delete'])->name('deleteUserDeviceToken');
            Route::get('read/{id?}', [UserDeviceTokenController::class, 'read'])->name('readUserDeviceToken');
            Route::put('update', [UserDeviceTokenController::class, 'update'])->name('updateUserDeviceToken');
        });

        Route::prefix('contact_us')->group(function () {
            Route::delete('delete', [ContactUsController::class, 'delete'])->name('deleteContactUs');
            Route::get('read/{id?}', [ContactUsController::class, 'read'])->name('readContactUs');
            Route::put('update', [ContactUsController::class, 'update'])->name('updateContactUs');
        });

    });

});
