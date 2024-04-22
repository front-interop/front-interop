<?php
declare(strict_types=1);

namespace FrontInterop;

/**
 * Wraps a response with the logic needed to send or emit that response.
 */
interface ResponseHandlerInterface
{
    public function handleResponse() : void;
}
