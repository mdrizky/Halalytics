<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function successResponse(mixed $data = null, string $message = 'OK', int $status = 200, array $extra = [])
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $extra), $status);
    }

    protected function errorResponse(string $message = 'Terjadi kesalahan.', int $status = 400, mixed $data = null, array $extra = [])
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $extra), $status);
    }
}
