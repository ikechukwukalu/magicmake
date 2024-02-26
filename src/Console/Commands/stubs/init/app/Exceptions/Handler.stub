<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // if (app()->bound('sentry')) {
            //     app('sentry')->captureException($e);
            // }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                $response = responseData(false, Response::HTTP_UNAUTHORIZED, trans('auth.unauthenticated'));
                return httpJsonResponse($response);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            $response = responseData(false,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage(),
                $e->getTrace()
            );
            return httpJsonResponse($response);
        });
    }
}
