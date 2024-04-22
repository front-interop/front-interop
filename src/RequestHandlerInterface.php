<?php
declare(strict_types=1);

namespace FrontInterop;

/**
 * Receives the incoming request, then delegates it to a callable that
 * will  convert it to a response, and returns that response wrapped in
 * a ResponseHandler.
 */
interface RequestHandlerInterface
{
    public function handleRequest() : ResponseHandlerInterface;
}
