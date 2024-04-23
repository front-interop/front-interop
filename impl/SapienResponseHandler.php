<?php
declare(strict_types=1);

namespace FrontInterop\Impl;

use FrontInterop\ResponseHandlerInterface;
use Sapien\Response;

class SapienResponseHandler implements ResponseHandlerInterface
{
    public function __construct(protected Response $response)
    {
    }

    public function handleResponse() : void
    {
        $this->response->send();
    }
}
