<?php
declare(strict_types=1);

namespace FrontInterop\ResponseHandler;

use FrontInterop\AssertionMethods;
use Nyholm\Psr7\Factory\Psr17Factory;

class PsrResponseHandlerTest extends \PHPUnit\Framework\TestCase
{
    use AssertionMethods;

    public function test() : void
    {
        $psr17Factory = new Psr17Factory();
        $responseBody = $psr17Factory->createStream('Hello World!');
        $response = $psr17Factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html')
            ->withBody($responseBody);
        $this->assertResponse(
            new PsrResponseHandler($response),
            200,
            ['Content-type: text/html;charset=UTF-8'],
            'Hello World!',
        );
    }
}
