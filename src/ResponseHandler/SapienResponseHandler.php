<?php
declare(strict_types=1);

namespace FrontInterop\ResponseHandler;

use FrontInterop\ResponseHandler;
use Sapien\Response;

class SapienResponseHandler implements ResponseHandler
{
    public function __construct(protected Response $response)
    {
    }

    public function handleResponse() : void
    {
        $this->response->send();
    }
}
