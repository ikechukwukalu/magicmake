<?php

namespace App\Http\Middleware;

use App\Enums\UserModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $allowed = [
            UserModel::ADMIN->value,
        ];

        if (in_array($user->model, $allowed)) {
            return $next($request);
        }

        $response = responseData(false, Response::HTTP_LOCKED, trans('general.not_allowed.access'));

        return httpJsonResponse($response);
    }
}
