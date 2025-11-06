<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function success($data = null, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            if (is_array($data) && isset($data['data'])) {
                // If data already has 'data' key, merge it
                $response = array_merge($response, $data);
            } elseif (is_array($data)) {
                // Process array - check for JsonResource instances
                $processedData = [];
                foreach ($data as $key => $value) {
                    if ($value instanceof JsonResource) {
                        $processedData[$key] = $value->resolve();
                    } else {
                        $processedData[$key] = $value;
                    }
                }
                $response['data'] = $processedData;
            } elseif ($data instanceof JsonResource) {
                $response['data'] = $data->resolve(request());
            } else {
                $response['data'] = $data;
            }
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function error(string $message, int $statusCode = 400): JsonResponse
    {
        return new JsonResponse([
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Return a resource response.
     *
     * @param JsonResource $resource
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function resource(JsonResource $resource, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'data' => $resource->resolve(),
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * Return a collection response.
     *
     * @param \Illuminate\Http\Resources\Json\ResourceCollection $collection
     * @param string|null $key
     * @param string|null $message
     * @param int $statusCode
     * @param mixed|null $paginator
     * @return JsonResponse
     */
    protected function collection($collection, ?string $key = null, ?string $message = null, int $statusCode = 200, $paginator = null): JsonResponse
    {
        $data = $collection->resolve();
        $pagination = null;

        // Check paginator for pagination info
        if ($paginator && method_exists($paginator, 'currentPage')) {
            $pagination = [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ];
        }

        if ($key) {
            $response = [
                $key => $data,
            ];
        } else {
            $response = [
                'data' => $data,
            ];
        }

        if ($pagination) {
            $response['pagination'] = $pagination;
        }

        if ($message) {
            $response['message'] = $message;
        }

        return new JsonResponse($response, $statusCode);
    }
}
