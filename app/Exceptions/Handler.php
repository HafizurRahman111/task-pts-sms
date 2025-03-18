<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Handle unauthenticated requests
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'You must log in to access this resource.',
            ], 401); // 401 Unauthorized
        }

        // Handle validation errors
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $exception->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        // Handle model not found errors
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'The requested resource was not found.',
            ], 404); // 404 Not Found
        }

        // Handle route not found errors
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'The requested route was not found.',
            ], 404); // 404 Not Found
        }

        // Handle all other exceptions
        return response()->json([
            'message' => 'An unexpected error occurred. Please try again later.',
            'error' => $exception->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}