<?php
declare(strict_types=1);

namespace FrontInterop;

use FrontInterop\Example\Container;

/**
 * @backupGlobals enabled
 */
class FrontInteropTest extends \PHPUnit\Framework\TestCase
{
    use AssertionMethods;

    public function testFound() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/user/123';
        $this->assertResponse(
            $this->handleRequest(),
            200,
            ['content-type: application/json'],
            '{"user":{"id":"123"}}',
        );
    }

    public function testNotFound() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/users';
        $this->assertResponse(
            $this->handleRequest(),
            404,
            ['content-type: application/json'],
            '{"errors":[{"status":"404","title":"Not Found."}]}',
        );
    }

    public function testMethodNotAllowed() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'PATCH';
        $_SERVER['REQUEST_URI'] = '/user/123';
        $this->assertResponse(
            $this->handleRequest(),
            405,
            ['allow: GET', 'content-type: application/json'],
            '{"errors":{"status":405,"title":"Method not allowed.","detail":"Allow: GET"}}',
        );
    }

    protected function handleRequest() : ResponseHandlerInterface
    {
        $container = new Example\Container();
        $requestHandler = $container->get(RequestHandlerInterface::class);
        return $requestHandler->handleRequest();
    }
}
