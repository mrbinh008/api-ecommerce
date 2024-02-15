<?php
/** Response custom */
if (!function_exists('responseCustom')) {
    function responseCustom(mixed $data = [], int $status = 200, string $message = null, mixed $error = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'error' => $error
        ], $status);
    }
}

/** paginate custom */

if (!function_exists('responsePaginate')) {
    function responsePaginate(mixed $paginate, mixed $data = [], int $status = 200, string $message = null, mixed $error = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'meta' => [
                'current_page' => $paginate->currentPage(),
                'last_page' => $paginate->lastPage(),
                'from' => $paginate->firstItem(),
                'to' => $paginate->lastItem(),
                'nextPage' => $paginate->nextPageUrl(),
                'prevPage' => $paginate->previousPageUrl(),
                'per_page' => $paginate->perPage(),
                'total_page' => $paginate->total(),
            ],
            'message' => $message,
            'error' => $error
        ], $status);
    }
}


