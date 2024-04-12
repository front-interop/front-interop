<?php
declare(strict_types=1);

namespace FrontInterop\ResponseHandler;

use FrontInterop\AssertionMethods;
use FrontInterop\Example;
use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseHandlerTest extends \PHPUnit\Framework\TestCase
{
    use AssertionMethods;

    public function test() : void
    {
        $response = new Response(
            'Hello World!',
            Response::HTTP_OK,
            ['content-type' => 'text/html'],
        );
        $this->assertResponse(
            new Example\ExampleSymfonyResponseHandler($response),
            200,
            [
                'Content-type: text/html;charset=UTF-8',
                'Cache-Control: no-cache, private',
            ],
            'Hello World!',
        );
    }
}
