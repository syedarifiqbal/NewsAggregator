<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success($data = null, ?string $message = null, int $code = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message, int $code = 400)
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }
}
