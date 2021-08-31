<?php

namespace App\Traits;

trait ResponseAPI
{

    private function coreResponse(string $message, int $statusCode, bool $isSuccess, $data, $errorValidator)
    {
        if($isSuccess) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'results' => $data
            ], $statusCode);
        } else {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error_validator' => $errorValidator,
            ], $statusCode);
        }
    }

    public function success(string $message, int $statusCode, $data = [])
    {
        return $this->coreResponse($message, $statusCode, true, $data, []);
    }

    public function error(string $message, int $statusCode, $errorValidator = [])
    {
        if($statusCode == 0)
            $statusCode = 500;
        return $this->coreResponse($message, $statusCode, false, [], $errorValidator);
    }
}