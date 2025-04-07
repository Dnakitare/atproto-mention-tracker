<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            // Log all exceptions with context
            \Log::error('Application Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        });

        // Handle AT Protocol specific exceptions
        $this->renderable(function (ATProtocolException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => 'AT Protocol Error',
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ], 500);
            }
        });

        // Handle validation exceptions
        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => 'Validation Error',
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Handle authentication exceptions
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => 'Unauthenticated',
                    'message' => 'You are not authenticated.',
                ], 401);
            }
        });

        // Handle not found exceptions
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => 'Not Found',
                    'message' => 'The requested resource was not found.',
                ], 404);
            }
        });

        // Handle model not found exceptions
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => 'Not Found',
                    'message' => 'The requested resource was not found.',
                ], 404);
            }
        });

        // Handle general HTTP exceptions
        $this->renderable(function (HttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => 'HTTP Error',
                    'message' => $e->getMessage(),
                    'code' => $e->getStatusCode(),
                ], $e->getStatusCode());
            }
        });
    }
} 