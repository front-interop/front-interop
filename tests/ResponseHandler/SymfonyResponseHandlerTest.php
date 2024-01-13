<?php
declare(strict_types=1);

namespace FrontInterop\ResponseHandler;

use FrontInterop\AssertionMethods;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseHandlerTest extends \PHPUnit\Framework\TestCase
{
    use AssertionMethods;

    public function test() : void
    {
        $request = Request::createFromGlobals();
        $response = new Response(
            'Hello World!',
            Response::HTTP_OK,
            ['content-type' => 'text/html'],
        );
        $this->assertResponse(
            new SymfonyResponseHandler($response, $request),
            200,
            [
                'Content-Type: text/html; charset=UTF-8',
                'Cache-Control: no-cache, private',
            ],
            'Hello World!',
        );
    }
}
