<?php
declare(strict_types=1);

namespace FrontInterop\ResponseHandler;

use FrontInterop\AssertionMethods;
use Sapien\Response;

class SapienResponseHandlerTest extends \PHPUnit\Framework\TestCase
{
    use AssertionMethods;

    public function test() : void
    {
        $response = new Response();
        $response->setCode(200);
        $response->setHeader('content-type', 'text/html');
        $response->setContent('Hello World!');
        $this->assertResponse(
            new SapienResponseHandler($response),
            200,
            ['Content-type: text/html;charset=UTF-8'],
            'Hello World!',
        );
    }
}
