<?php
declare(strict_types=1);

namespace FrontInterop\Impl;

use FrontInterop\DelegateInterface;
use FrontInterop\DelegatorInterface;
use FrontInterop\RequestHandlerInterface;
use FrontInterop\ResponseHandlerInterface;
use SapienResponse;
use Throwable;

class RequestHandler implements RequestHandlerInterface
{
    public function __construct(
        protected DelegatorInterface $delegator,
        protected ResponseHandlerFactory $responseHandlerFactory,
    ) {
    }

    public function handleRequest() : ResponseHandlerInterface
    {
        try {
            $delegate = $this->delegator->delegateRequest();
            $response = $this->getResponse($delegate);
        } catch (Throwable $e) {
            $delegate = $this->delegator->delegateThrowable($e);
            $response = $this->getResponse($delegate);
        }

        return $this->responseHandlerFactory->newResponseHandler($response);
    }

    protected function getResponse(DelegateInterface $delegate) : object
    {
        $callable = $delegate->getCallable();
        $arguments = $delegate->getArguments();
        return $callable(...$arguments);
    }
}
