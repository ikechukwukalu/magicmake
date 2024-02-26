<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckEmailVerification
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user->model !== Customer::class) {
            return $next($request);
        }

        if ($user->email_verified_at) {
            return $next($request);
        }

        $data = responseData(false, Response::HTTP_LOCKED, trans('general.not_verified.email'));

        return httpJsonResponse($data);
    }
}
