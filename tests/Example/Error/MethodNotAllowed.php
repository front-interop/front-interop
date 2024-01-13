<?php
declare(strict_types=1);

namespace FrontInterop\Example\Error;

use Sapien\Response\JsonResponse;

class MethodNotAllowed
{
    /**
     * @param string[] $allow
     */
    public function __invoke(array $allow) : JsonResponse
    {
        $allowed = implode(',', $allow);
        $response = new JsonResponse();
        $response->setCode(405);
        $response->setHeader('allow', $allowed);
        $response->setJson([
            'errors' => [
                'status' => 405,
                'title' => 'Method not allowed.',
                'detail' => 'Allow: ' . $allowed,
            ],
        ]);
        return $response;
    }
}
