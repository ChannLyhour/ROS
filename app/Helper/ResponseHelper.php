<?php

if (!function_exists('response_success')) {
    /**
     * Return a success response
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    function response_success(
        string $message = 'Success',
        mixed $data = null,
        int $statusCode = 200
    ) {
        return \App\Services\ResponseService::success($message, $data, $statusCode);
    }
}

if (!function_exists('response_error')) {
    /**
     * Return an error response
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    function response_error(
        string $message = 'Error',
        mixed $data = null,
        int $statusCode = 400
    ) {
        return \App\Services\ResponseService::error($message, $data, $statusCode);
    }
}

if (!function_exists('response_not_found')) {
    /**
     * Return a not found response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    function response_not_found(string $message = 'Not Found') {
        return \App\Services\ResponseService::notFound($message);
    }
}

if (!function_exists('response_unauthorized')) {
    /**
     * Return an unauthorized response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    function response_unauthorized(string $message = 'Unauthorized') {
        return \App\Services\ResponseService::unauthorized($message);
    }
}

if (!function_exists('response_validation_error')) {
    /**
     * Return a validation error response
     *
     * @param array $errors
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    function response_validation_error(array $errors, string $message = 'Validation Error') {
        return \App\Services\ResponseService::validationError($errors, $message);
    }
}

if (!function_exists('response_server_error')) {
    /**
     * Return a server error response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    function response_server_error(string $message = 'Server Error') {
        return \App\Services\ResponseService::serverError($message);
    }
}

if (!function_exists('response_forbidden')) {
    /**
     * Return a forbidden response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    function response_forbidden(string $message = 'Forbidden') {
        return \App\Services\ResponseService::forbidden($message);
    }
}

if (!function_exists('response_fix_applied')) {
    /**
     * Return a fix applied response
     *
     * @param string $message
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    function response_fix_applied(string $message = 'Issue fixed successfully', mixed $data = null) {
        return \App\Services\ResponseService::fixApplied($message, $data);
    }
}

if (!function_exists('response_fix_failed')) {
    /**
     * Return a fix failed response
     *
     * @param string $message
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    function response_fix_failed(string $message = 'Failed to fix the issue', mixed $data = null) {
        return \App\Services\ResponseService::fixFailed($message, $data);
    }
}

if (!function_exists('response_requires_fix')) {
    /**
     * Return a requires fix response
     *
     * @param string $message
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    function response_requires_fix(string $message = 'This item requires fixing', mixed $data = null) {
        return \App\Services\ResponseService::requiresFix($message, $data);
    }
}
