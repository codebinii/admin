<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\CanalSoporte;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Centralized JSON response builder.
 *
 * All user-facing strings are resolved from lang/{locale}/api.php
 * based on APP_LOCALE in .env (default: en).
 *
 * Success envelope:
 * {
 *   "success": true,
 *   "message": "...",
 *   "data": {...}|[...],
 *   "meta": { "pagination": {...} }
 * }
 *
 * Error envelope (RFC 7807):
 * {
 *   "success": false,
 *   "status": 422,
 *   "title": "...",
 *   "detail": "...",
 *   "errors": { "field": ["msg"] }
 * }
 */
final class ApiResponse
{
    // ──────────────────────────────────────────────────────────────────
    // Success
    // ──────────────────────────────────────────────────────────────────

    public static function ok(mixed $data = null, string $message = ''): JsonResponse
    {
        return self::success($data, $message ?: self::t('ok'), 200);
    }

    public static function created(mixed $data = null, string $message = ''): JsonResponse
    {
        return self::success($data, $message ?: self::t('created'), 201);
    }

    public static function noContent(string $message = ''): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?: self::t('no_content'),
        ], 204);
    }

    public static function paginated(LengthAwarePaginator $paginator, string $message = ''): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?: self::t('ok'),
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
    // Errors (RFC 7807)
    // ──────────────────────────────────────────────────────────────────

    public static function badRequest(string $detail = ''): JsonResponse
    {
        return self::problem(400, 'Bad Request', $detail ?: self::t('bad_request'));
    }

    public static function unauthorized(string $detail = ''): JsonResponse
    {
        return self::problem(401, 'Unauthorized', $detail ?: self::t('unauthorized'));
    }

    public static function forbidden(string $detail = ''): JsonResponse
    {
        return self::problem(403, 'Forbidden', $detail ?: self::t('forbidden'));
    }

    public static function notFound(string $model = ''): JsonResponse
    {
        $detail = $model
            ? self::t('not_found', ['model' => $model])
            : self::t('not_found', ['model' => 'Resource']);

        return self::problem(404, 'Not Found', $detail);
    }

    public static function methodNotAllowed(string $detail = ''): JsonResponse
    {
        return self::problem(405, 'Method Not Allowed', $detail ?: self::t('bad_request'));
    }

    public static function validationError(array $errors, string $detail = ''): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status'  => 422,
            'title'   => 'Unprocessable Entity',
            'detail'  => $detail ?: self::t('validation_detail'),
            'errors'  => $errors,
        ], 422);
    }

    public static function tooManyRequests(string $detail = ''): JsonResponse
    {
        return self::problem(429, 'Too Many Requests', $detail ?: self::t('too_many_requests'));
    }

    public static function serverError(string $detail = ''): JsonResponse
    {
        return self::problem(500, 'Internal Server Error', $detail ?: self::t('server_error'));
    }

    public static function routeNotFound(string $path, int $status = 404): JsonResponse
    {
        [$titleKey, $detailKey] = $status === 405
            ? ['method_not_allowed_title', 'method_not_allowed_detail']
            : ['route_not_found_title',    'route_not_found_detail'];

        try {
            $support = CanalSoporte::where('activo', true)->get(['canal', 'detalle', 'agente']);
        } catch (\Throwable) {
            $support = [];
        }

        return response()->json([
            'success'    => false,
            'status'     => $status,
            'title'      => self::t($titleKey),
            'detail'     => self::t($detailKey, ['path' => $path]),
            'suggestion' => self::t('route_suggestion'),
            'support'    => $support,
        ], $status);
    }

    // ──────────────────────────────────────────────────────────────────
    // Internal
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

    private static function t(string $key, array $replace = []): string
    {
        return (string) trans("api.{$key}", $replace);
    }
}
