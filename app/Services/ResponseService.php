<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    /**
     * Send a success response
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success(
        string $message = 'Success',
        mixed $data = null,
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Send an error response
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function error(
        string $message = 'Error',
        mixed $data = null,
        int $statusCode = 400
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Send a not found response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Not Found'): JsonResponse
    {
        return self::error($message, null, 404);
    }

    /**
     * Send an unauthorized response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, null, 401);
    }

    /**
     * Send a validation error response
     *
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    public static function validationError(
        array $errors,
        string $message = 'Validation Error'
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => ['errors' => $errors],
        ], 422);
    }

    /**
     * Send a server error response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function serverError(string $message = 'Server Error'): JsonResponse
    {
        return self::error($message, null, 500);
    }

    /**
     * Send a forbidden response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, null, 403);
    }

    /**
     * Send a fix applied response
     *
     * @param string $message
     * @param mixed $data
     * @return JsonResponse
     */
    public static function fixApplied(
        string $message = 'Issue fixed successfully',
        mixed $data = null
    ): JsonResponse {
        return response()->json([
            'status' => 'fixed',
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    /**
     * Send a fix failed response
     *
     * @param string $message
     * @param mixed $data
     * @return JsonResponse
     */
    public static function fixFailed(
        string $message = 'Failed to fix the issue',
        mixed $data = null
    ): JsonResponse {
        return self::error($message, $data, 400);
    }

    /**
     * Send a requires fix response
     *
     * @param string $message
     * @param mixed $data
     * @return JsonResponse
     */
    public static function requiresFix(
        string $message = 'This item requires fixing',
        mixed $data = null
    ): JsonResponse {
        return response()->json([
            'status' => 'requires_fix',
            'message' => $message,
            'data' => $data,
        ], 400);
    }
}
