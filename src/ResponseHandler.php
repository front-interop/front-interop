<?php
declare(strict_types=1);

namespace FrontInterop;

interface ResponseHandler
{
    public function handleResponse() : void;
}
