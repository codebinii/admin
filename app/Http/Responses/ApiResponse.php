<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\CanalSoporte;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Centralized JSON response builder.
 *
 * Success envelope:
 * {
 *   "success": true,
 *   "message": "...",
 *   "data": {...}|[...],
 *   "meta": { "pagination": {...} }   // only when paginated
 * }
 *
 * Error envelope (RFC 7807 — Problem Details for HTTP APIs):
 * {
 *   "success": false,
 *   "status": 422,
 *   "title": "Unprocessable Entity",
 *   "detail": "...",
 *   "errors": { "field": ["msg"] }    // only on validation errors
 * }
 */
final class ApiResponse
{
    // ──────────────────────────────────────────────────────────────────
    // Success responses
    // ──────────────────────────────────────────────────────────────────

    public static function ok(mixed $data = null, string $message = 'OK'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    public static function created(mixed $data = null, string $message = 'Resource created successfully.'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    public static function noContent(string $message = 'No content.'): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message], 204);
    }

    public static function paginated(LengthAwarePaginator $paginator, string $message = 'OK'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'pagination' => [
                    'total'        => $paginator->total(),
                    'per_page'     => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'from'         => $paginator->firstItem(),
                    'to'           => $paginator->lastItem(),
                ],
            ],
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────
    // Error responses  (RFC 7807)
    // ──────────────────────────────────────────────────────────────────

    public static function badRequest(string $detail = 'Bad request.'): JsonResponse
    {
        return self::problem(400, 'Bad Request', $detail);
    }

    public static function unauthorized(string $detail = 'Unauthenticated.'): JsonResponse
    {
        return self::problem(401, 'Unauthorized', $detail);
    }

    public static function forbidden(string $detail = 'This action is unauthorized.'): JsonResponse
    {
        return self::problem(403, 'Forbidden', $detail);
    }

    public static function notFound(string $detail = 'Resource not found.'): JsonResponse
    {
        return self::problem(404, 'Not Found', $detail);
    }

    public static function methodNotAllowed(string $detail = 'Method not allowed.'): JsonResponse
    {
        return self::problem(405, 'Method Not Allowed', $detail);
    }

    public static function routeNotFound(string $path, int $status = 404): JsonResponse
    {
        $title = $status === 405 ? 'Method Not Allowed' : 'Route Not Found';

        $detail = $status === 405
            ? "The HTTP method used is not allowed for: {$path}"
            : "The path '{$path}' does not exist in this API.";

        try {
            $support = CanalSoporte::where('activo', true)->get(['canal', 'detalle', 'agente']);
        } catch (\Throwable) {
            $support = [];
        }

        return response()->json([
            'success'    => false,
            'status'     => $status,
            'title'      => $title,
            'detail'     => $detail,
            'suggestion' => 'Please verify the endpoint path and HTTP method, or contact our support team.',
            'support'    => $support,
        ], $status);
    }

    public static function validationError(array $errors, string $detail = 'The given data was invalid.'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status'  => 422,
            'title'   => 'Unprocessable Entity',
            'detail'  => $detail,
            'errors'  => $errors,
        ], 422);
    }

    public static function tooManyRequests(string $detail = 'Too many requests.'): JsonResponse
    {
        return self::problem(429, 'Too Many Requests', $detail);
    }

    public static function serverError(string $detail = 'An unexpected error occurred.'): JsonResponse
    {
        return self::problem(500, 'Internal Server Error', $detail);
    }

    // ──────────────────────────────────────────────────────────────────
    // Internal builders
    // ──────────────────────────────────────────────────────────────────

    private static function success(mixed $data, string $message, int $status): JsonResponse
    {
        $body = ['success' => true, 'message' => $message];

        if ($data !== null) {
            $body['data'] = $data;
        }

        return response()->json($body, $status);
    }

    private static function problem(int $status, string $title, string $detail): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status'  => $status,
            'title'   => $title,
            'detail'  => $detail,
        ], $status);
    }
}
