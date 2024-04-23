<?php
declare(strict_types=1);

namespace FrontInterop\Fake\Action;

use Sapien\Response\JsonResponse;

class GetUserAction
{
    public function __invoke(string $id) : JsonResponse
    {
        $response = new JsonResponse();
        $response->setJson(value: ['user' => ['id' => $id]]);
        return $response;
    }
}
