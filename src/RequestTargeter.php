<?php
declare(strict_types=1);

namespace FrontInterop;

interface RequestTargeter
{
    public function getRequestTarget() : RequestTarget;
}
