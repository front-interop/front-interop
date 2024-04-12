<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use FrontInterop\RequestHandler;
use FrontInterop\RequestTargeter;
use FrontInterop\ResponseHandler;

class ExampleRequestHandler implements RequestHandler
{
    public function __construct(
        protected RequestTargeter $requestTargeter,
        protected ExampleResponseHandlerFactory $responseHandlerFactory,
    ) {
    }

    public function handleRequest() : ResponseHandler
    {
        $requestTarget = $this->requestTargeter->getRequestTarget();
        $callable = $requestTarget->getCallable();
        $arguments = $requestTarget->getArguments();
        $response = $callable(...$arguments);
        return $this->responseHandlerFactory->newResponseHandler($response);
    }
}
