<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseAPIController extends Controller
{
    /**
     * Success response method
     */
    public function sendResponse($data, $message = 'Success', $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ];

        return response()->json($response, $code);
    }

    /**
     * Error response method
     */
    public function sendError($message, $errorCode = null, $code = 400, $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];

        if (!empty($errorCode)) {
            $response['error_code'] = $errorCode;
        }

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation error response method
     */
    public function sendValidationError($errors, $message = 'Validation failed'): JsonResponse
    {
        return $this->sendError($message, 'VALIDATION_FAILED', 400, $errors);
    }

    /**
     * Not found response method
     */
    public function sendNotFound($message = 'Resource not found'): JsonResponse
    {
        return $this->sendError($message, 'NOT_FOUND', 404);
    }

    /**
     * Unauthorized response method
     */
    public function sendUnauthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this->sendError($message, 'UNAUTHORIZED', 401);
    }

    /**
     * Server error response method
     */
    public function sendServerError($message = 'Internal server error'): JsonResponse
    {
        return $this->sendError($message, 'SERVER_ERROR', 500);
    }

    /**
     * Created response method
     */
    public function sendCreated($data, $message = 'Created successfully'): JsonResponse
    {
        return $this->sendResponse($data, $message, 201);
    }

    /**
     * Get pagination meta data
     */
    protected function getPaginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }
}