<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use FrontInterop\DelegateInterface;

class Delegate implements DelegateInterface
{
    /**
     * @param callable $callable
     */
    public function __construct(
        protected mixed $callable,
        protected array $arguments = [],
    ) {
    }

    public function getCallable() : callable
    {
        return $this->callable;
    }

    public function getArguments() : array
    {
        return $this->arguments;
    }
}
