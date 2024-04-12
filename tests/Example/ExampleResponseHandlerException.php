<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use Exception;
use FrontInterop\FrontException;

class ExampleResponseHandlerException extends Exception implements FrontException
{
}
