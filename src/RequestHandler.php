<?php
declare(strict_types=1);

namespace FrontInterop;

interface RequestHandler
{
    public function handleRequest() : ResponseHandler;
}
