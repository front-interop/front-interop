<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use FrontInterop\ResponseHandler;
use Sapien\Response;

class ExampleSapienResponseHandler implements ResponseHandler
{
    public function __construct(protected Response $response)
    {
    }

    public function handleResponse() : void
    {
        $this->response->send();
    }
}
