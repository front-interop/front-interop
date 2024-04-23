<?php
declare(strict_types=1);

namespace FrontInterop\Fake\Error;

use Sapien\Response\JsonResponse;

class RouteNotFoundError
{
    public function __invoke() : JsonResponse
    {
        $response = new JsonResponse();
        $response->setCode(404);
        $response->setJson([
            'errors' => [['status' => '404', 'title' => 'Not Found.']],
        ]);
        return $response;
    }
}
