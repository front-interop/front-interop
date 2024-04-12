<?php
declare(strict_types=1);

namespace FrontInterop;

interface RequestTarget
{
    public function getCallable() : callable;

    public function getArguments() : array;
}
