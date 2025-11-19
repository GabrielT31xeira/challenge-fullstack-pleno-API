<?php

namespace App\Support;

class ApiResponse
{
    public static function success($data = null)
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public static function error(string $message, array $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    public static function serverError(string $message, string $errors)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => [$errors]
        ]);
    }
}