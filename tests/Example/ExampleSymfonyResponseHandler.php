<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use FrontInterop\ResponseHandler;
use Symfony\Component\HttpFoundation\Response;

class ExampleSymfonyResponseHandler implements ResponseHandler
{
    public function __construct(protected Response $response)
    {
    }

    public function handleResponse() : void
    {
        $this->response->send();
    }
}
