<?php
declare(strict_types=1);

namespace FrontInterop;

/**
 * Describes the callable, and the arguments for that callable,
 * that will generate and return a response.
 */
interface DelegateInterface
{
    public function getCallable() : callable;

    /**
     * @return mixed[]
     */
    public function getArguments() : array;
}
