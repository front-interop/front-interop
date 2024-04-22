<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use FrontInterop\ResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseHandler implements ResponseHandlerInterface
{
    public function __construct(protected Response $response)
    {
    }

    public function handleResponse() : void
    {
        $this->response->send();
    }
}
