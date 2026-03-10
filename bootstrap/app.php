<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), $e->getMessage());
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponse::unauthorized('Unauthenticated. Please log in.');
        });

        $exceptions->render(function (AuthorizationException $e) {
            return ApiResponse::forbidden($e->getMessage() ?: 'This action is unauthorized.');
        });

        $exceptions->render(function (ModelNotFoundException $e) {
            $model = class_basename($e->getModel());
            return ApiResponse::notFound("{$model} not found.");
        });

        $exceptions->render(function (NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            return ApiResponse::routeNotFound($request->path(), 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, \Illuminate\Http\Request $request) {
            return ApiResponse::routeNotFound($request->path(), 405);
        });

        $exceptions->render(function (TooManyRequestsHttpException $e) {
            return ApiResponse::tooManyRequests('Too many requests. Please slow down.');
        });

        $exceptions->render(function (Throwable $e) {
            return ApiResponse::serverError(
                app()->isProduction() ? 'An unexpected error occurred.' : $e->getMessage()
            );
        });

    })->create();
