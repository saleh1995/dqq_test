<?php

namespace App\Http\Traits;

trait ApiResponseTrait
{
    public function apiResponse($data = null, $message = null, $status = 200, $success = true, $headers = [])
    {
        $array = [
            'data' => $data,
            'message' => $message,
            'status' => $status,
            'success' => $success,
        ];
        return response()->json($array, $status, $headers);
    }


    public function apiResponseUnauthorized($data = null, $message = 'ERR_001', $status = 401, $success = false)
    {
        return $this->apiResponse($data, $message, $status, $success);
    }



    public function apiResponseError($data = null, $message = 'ERR_002', $status = 400, $success = false)
    {
        return $this->apiResponse($data, $message, $status, $success);
    }


    public function apiResponseException($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $response = [];

        switch ($statusCode) {
            case 401:
                $response['message'] = 'ERR_001';
                break;
            case 403:
                $response['message'] = 'ERR_003';
                break;
            case 404:
                $response['message'] = 'ERR_004';
                break;
            case 405:
                $response['message'] = 'ERR_005';
                break;
            case 422:
                $response['message'] = 'ERR_006';
                $response['errors'] = $exception->original['errors'];
                break;
            case 429:
                $response['message'] = 'ERR_007';
                break;
            default:
                $response['message'] = ($statusCode == 500) ? 'ERR_008' : $exception->getMessage();
                break;
        }

        // if (config('app.debug')) {
        //     $response['trace'] = $exception->getTrace() ?? null;
        //     $response['code'] = $exception->getCode() ?? null;
        // }

        $response['status'] = $statusCode;

        return $this->apiResponseError($response['errors'] ?? null, $response['message'], $statusCode);
    }
}
