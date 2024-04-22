<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use Exception;
use FrontInterop\ExceptionInterface;

class ResponseHandlerException extends Exception implements ExceptionInterface
{
}
