<?php
declare(strict_types=1);

namespace FrontInterop\Impl;

use FrontInterop\ResponseHandlerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Sapien\Response as SapienResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResponseHandlerFactory
{
    public function newResponseHandler(mixed $response) : ResponseHandlerInterface
    {
        switch (true) {
            case $response instanceof PsrResponse:
                return new PsrResponseHandler($response);

            case $response instanceof SapienResponse:
                return new SapienResponseHandler($response);

            case $response instanceof SymfonyResponse:
                return new SymfonyResponseHandler($response);

            default:
                $type = get_class($type);
                throw new FrontInteropException("Unknown response type: {$type}");
        };
    }
}
