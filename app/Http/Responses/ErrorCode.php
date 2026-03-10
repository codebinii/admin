<?php

declare(strict_types=1);

namespace App\Http\Responses;

/**
 * Internal error codes for 500-level responses.
 *
 * Format: ERR_{CATEGORY}_{DETAIL}
 * Clients can use these codes to open support tickets.
 */
final class ErrorCode
{
    // ── Database ──────────────────────────────────────────────────────
    const DB_CONNECTION      = 'ERR_DB_001';
    const DB_QUERY_FAILED    = 'ERR_DB_002';
    const DB_DEADLOCK        = 'ERR_DB_003';
    const DB_CONSTRAINT      = 'ERR_DB_004';
    const DB_TIMEOUT         = 'ERR_DB_005';

    // ── Authentication / Token ────────────────────────────────────────
    const AUTH_TOKEN_INVALID = 'ERR_AUTH_001';
    const AUTH_TOKEN_EXPIRED = 'ERR_AUTH_002';
    const AUTH_HASH_FAILED   = 'ERR_AUTH_003';

    // ── File / Storage ────────────────────────────────────────────────
    const FILE_NOT_FOUND     = 'ERR_FILE_001';
    const FILE_UNREADABLE    = 'ERR_FILE_002';
    const FILE_UPLOAD_FAILED = 'ERR_FILE_003';
    const STORAGE_FULL       = 'ERR_FILE_004';

    // ── Configuration ─────────────────────────────────────────────────
    const CONFIG_MISSING     = 'ERR_CFG_001';
    const CONFIG_INVALID     = 'ERR_CFG_002';
    const ENV_MISSING        = 'ERR_CFG_003';

    // ── External Services ─────────────────────────────────────────────
    const SERVICE_TIMEOUT    = 'ERR_SVC_001';
    const SERVICE_UNAVAILABLE = 'ERR_SVC_002';
    const SERVICE_BAD_RESPONSE = 'ERR_SVC_003';

    // ── Queue / Jobs ──────────────────────────────────────────────────
    const QUEUE_FAILED       = 'ERR_JOB_001';
    const JOB_MAX_ATTEMPTS   = 'ERR_JOB_002';

    // ── Generic ───────────────────────────────────────────────────────
    const UNEXPECTED         = 'ERR_GEN_001';
    const NULL_REFERENCE     = 'ERR_GEN_002';
    const TYPE_MISMATCH      = 'ERR_GEN_003';
    const LOGIC_ERROR        = 'ERR_GEN_004';

    /**
     * Maps an exception class to its error code.
     */
    public static function fromException(\Throwable $e): string
    {
        return match (true) {
            $e instanceof \Illuminate\Database\QueryException          => self::DB_QUERY_FAILED,
            $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => self::DB_QUERY_FAILED,
            str_contains($e->getMessage(), 'Connection refused')      => self::DB_CONNECTION,
            str_contains($e->getMessage(), 'Deadlock')                => self::DB_DEADLOCK,
            str_contains($e->getMessage(), 'timed out')               => self::DB_TIMEOUT,
            $e instanceof \TypeError                                   => self::TYPE_MISMATCH,
            $e instanceof \LogicException                              => self::LOGIC_ERROR,
            $e instanceof \ErrorException                              => self::NULL_REFERENCE,
            default                                                    => self::UNEXPECTED,
        };
    }

    /**
     * Returns a short human-readable description for a code.
     */
    public static function describe(string $code): string
    {
        return self::descriptions()[$code] ?? 'Unknown error.';
    }

    private static function descriptions(): array
    {
        return [
            // Database
            self::DB_CONNECTION      => 'Could not connect to the database.',
            self::DB_QUERY_FAILED    => 'A database query failed.',
            self::DB_DEADLOCK        => 'A database deadlock was detected.',
            self::DB_CONSTRAINT      => 'A database constraint was violated.',
            self::DB_TIMEOUT         => 'The database query timed out.',
            // Auth
            self::AUTH_TOKEN_INVALID => 'The authentication token is invalid.',
            self::AUTH_TOKEN_EXPIRED => 'The authentication token has expired.',
            self::AUTH_HASH_FAILED   => 'Password hashing failed.',
            // File
            self::FILE_NOT_FOUND     => 'A required file was not found.',
            self::FILE_UNREADABLE    => 'A file could not be read.',
            self::FILE_UPLOAD_FAILED => 'The file upload failed.',
            self::STORAGE_FULL       => 'Storage space is full.',
            // Config
            self::CONFIG_MISSING     => 'A required configuration key is missing.',
            self::CONFIG_INVALID     => 'A configuration value is invalid.',
            self::ENV_MISSING        => 'A required environment variable is not set.',
            // Services
            self::SERVICE_TIMEOUT    => 'An external service timed out.',
            self::SERVICE_UNAVAILABLE => 'An external service is unavailable.',
            self::SERVICE_BAD_RESPONSE => 'An external service returned an unexpected response.',
            // Queue
            self::QUEUE_FAILED       => 'A background job failed.',
            self::JOB_MAX_ATTEMPTS   => 'A job exceeded maximum retry attempts.',
            // Generic
            self::UNEXPECTED         => 'An unexpected error occurred.',
            self::NULL_REFERENCE     => 'A null reference error occurred.',
            self::TYPE_MISMATCH      => 'A type mismatch error occurred.',
            self::LOGIC_ERROR        => 'An application logic error occurred.',
        ];
    }
}
