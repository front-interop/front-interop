<?php
declare(strict_types=1);

namespace FrontInterop\ResponseHandler;

use FrontInterop\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseHandler implements ResponseHandler
{
    public function __construct(
        protected Response $response,
        protected ?Request $request = null,
    ) {
    }

    public function handleResponse() : void
    {
        if ($this->request) {
            $this->response->prepare($this->request);
        }

        $this->response->send();
    }
}
