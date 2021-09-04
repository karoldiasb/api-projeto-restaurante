<?php

namespace App\Traits;
use App\Enum\HttpStatusCode;

trait ResponseAPI
{
    private function coreResponse(int $statusCode, bool $isSuccess, $data, $errorValidator, string $message = '')
    {
        if($isSuccess) {
            return response()->json([
                'results' => $data
            ], $statusCode);
        } else {
            return response()->json([
                'message' => $message,
                'error_validator' => $errorValidator,
            ], $statusCode);
        }
    }

    public function success(int $statusCode, $data = [])
    {
        return $this->coreResponse(
            $statusCode, 
            true, 
            $data, 
            []
        );
    }

    public function error(int $statusCode, string $message = '', $errorValidator = [])
    {
        if($statusCode == 0)
            $statusCode = HttpStatusCode::INTERNAL_ERROR;

        return $this->coreResponse(
            $statusCode, 
            false, 
            [], 
            $errorValidator,
            $message
        );
    }
}