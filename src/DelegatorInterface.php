<?php
declare(strict_types=1);

namespace FrontInterop;

use Throwable;

/**
 * Encapsulates the logic to determine the delegate for logic
 * that will convert a request (or a Throwable) to a response.
 */
interface DelegatorInterface
{
    public function delegateRequest() : DelegateInterface;

    public function delegateThrowable(Throwable $e) : DelegateInterface;
}
