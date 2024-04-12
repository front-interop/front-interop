<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use FrontInterop\ResponseHandler;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Sapien\Response as SapienResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ExampleResponseHandlerFactory
{
    public function newResponseHandler(mixed $response) : ResponseHandler
    {
        switch (true) {
            case $response instanceof PsrResponse:
                return new ExamplePsrResponseHandler($response);

            case $response instanceof SapienResponse:
                return new ExampleSapienResponseHandler($response);

            case $response instanceof SymfonyResponse:
                return new ExampleSymfonyResponseHandler($response);

            default:
                $type = get_class($type);
                throw new ExampleResponseHandlerException("Unknown response type: {$type}");
        };
    }
}
