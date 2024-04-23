<?php
declare(strict_types=1);

namespace FrontInterop\Fake\Error;

use Sapien\Response\JsonResponse;
use Throwable;

class ServerError
{
    public function __invoke(Throwable $e) : JsonResponse
    {
        $response = new JsonResponse();
        $response->setCode(500);
        $response->setJson([
            'errors' => [
                'status' => 500,
                'title' => get_class($e),
                'detail' => $e->getMessage(),
            ],
        ]);
        return $response;
    }
}
