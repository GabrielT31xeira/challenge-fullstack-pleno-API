<?php

namespace App\Support;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    public static function success($data = null)
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public static function paginated(ResourceCollection $resource)
    {
        $response = $resource->response()->getData();

        return response()->json([
            'success' => true,
            'data' => $response->data,
            'meta' => [
                'current_page' => $response->meta->current_page ?? 1,
                'per_page' => $response->meta->per_page ?? 15,
                'total' => $response->meta->total ?? 0,
                'last_page' => $response->meta->last_page ?? 1
            ],
            'links' => [
                'first' => $response->links->first ?? null,
                'last' => $response->links->last ?? null,
                'prev' => $response->links->prev ?? null,
                'next' => $response->links->next ?? null
            ]
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