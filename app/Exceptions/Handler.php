<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;


class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    // protected $dontFlash = [
    //     'current_password',
    //     'password',
    //     'password_confirmation',
    // ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    // public function register()
    // {
    //     $this->reportable(function (Throwable $e) {
    //         //
    //     });
    // }

    public function render($request, Throwable $exception)
    {
        // Check if the request is an API request
        if ($request->is('api/*')) {
            // Handle specific exceptions
            if ($exception instanceof AuthenticationException) {
                // Return a JSON response for authentication errors
                return response()->json([
                    'message' => 'Unauthorized. Invalid or missing token.',
                    'error' => $exception->getMessage()
                ], 401);
            }

            // Handle validation exceptions
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'message' => 'Validation error.',
                    'errors' => $exception->errors()
                ], 422);
            }

            // Handle other exceptions
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $exception->getMessage()
            ], 500);
        }

        // For non-API requests, default to the parent render method
        return parent::render($request, $exception);
    }
}