<?php

namespace App;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse($data, $message = null, $meta = null, $statusCode = 200): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'=> $data,
            'errors' => null,
            'meta' => $meta
        ], $statusCode);
    }

    protected function errorResponse($message = null, $errors = null, $meta = null, $statusCode = 500): JsonResponse {
        return response()->json([
            'success' => false,
            'message'=> $message,
            'data' => null,
            'errors'=> $errors,
            'meta'=> $meta,
        ], $statusCode);
    }
}
