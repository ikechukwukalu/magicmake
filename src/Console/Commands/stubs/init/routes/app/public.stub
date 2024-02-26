<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ContactUsController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('register', [RegisterController::class, 'register'])->name('register');
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::get('verify/email/{id}', [VerificationController::class, 'verifyUserEmail'])->name('verification.verify');
    Route::post('forgot/password', [ForgotPasswordController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('reset/password', [ResetPasswordController::class, 'resetPassword'])->name('resetPassword');

    // contact us route
    Route::post('create', [ContactUsController::class, 'create'])->name('createContactUs');
});
