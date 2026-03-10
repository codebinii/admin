<?php

use App\Http\Middleware\ForceJsonResponse;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
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
        // Always respond with JSON — prevents HTML error pages on missing Accept header
        $middleware->api(prepend: [ForceJsonResponse::class]);

        // Pure API — never redirect unauthenticated requests to a login route.
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (Throwable $e, Request $request) {

            return match (true) {

                $e instanceof ValidationException =>
                    ApiResponse::validationError($e->errors(), trans('api.validation_detail')),

                $e instanceof AuthenticationException =>
                    ApiResponse::unauthorized(trans('api.unauthorized')),

                $e instanceof AuthorizationException =>
                    ApiResponse::forbidden($e->getMessage() ?: trans('api.forbidden')),

                $e instanceof ModelNotFoundException =>
                    ApiResponse::notFound(class_basename($e->getModel())),

                $e instanceof NotFoundHttpException =>
                    ApiResponse::routeNotFound($request->path(), 404),

                $e instanceof MethodNotAllowedHttpException =>
                    ApiResponse::routeNotFound($request->path(), 405),

                $e instanceof TooManyRequestsHttpException =>
                    ApiResponse::tooManyRequests(trans('api.too_many_requests')),

                default =>
                    ApiResponse::serverError(
                        app()->isProduction()
                            ? trans('api.server_error')
                            : $e->getMessage(),
                        $e
                    ),
            };
        });

    })->create();
