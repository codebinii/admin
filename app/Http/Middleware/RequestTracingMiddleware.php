<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\AppLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs every HTTP request and its response to the `requests` channel.
 *
 * Disable via LOG_TRACE_ENABLED=false in .env.
 * Adds X-Request-ID header to the response for cross-layer correlation.
 * Sensitive input fields are masked before writing to disk.
 */
final class RequestTracingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('logging.trace_enabled', true)) {
            return $next($request);
        }

        $requestId = Str::uuid()->toString();
        $startedAt = microtime(true);

        $request->headers->set('X-Request-ID', $requestId);

        AppLog::request('request.in', [
            'request_id' => $requestId,
            'method'     => $request->method(),
            'path'       => $request->path(),
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id'    => $request->user()?->id,
            'input'      => AppLog::mask($request->except(['_token'])),
        ]);

        $response = $next($request);

        AppLog::request('request.out', [
            'request_id'  => $requestId,
            'method'      => $request->method(),
            'path'        => $request->path(),
            'status'      => $response->getStatusCode(),
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            'user_id'     => $request->user()?->id,
        ]);

        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
