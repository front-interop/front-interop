<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use FastRoute\Dispatcher;
use FrontInterop\RequestHandler;
use FrontInterop\ResponseHandler;
use FrontInterop\ResponseHandler\SapienResponseHandler;
use Psr\Container\ContainerInterface;
use Sapien\Request;

class ExampleRequestHandler implements RequestHandler
{
    public function __construct(
        protected Request $request,
        protected Dispatcher $dispatcher,
        protected ContainerInterface $container,
    ) {
    }

    public function handleRequest() : ResponseHandler
    {
        $routeInfo = $this->dispatcher
            ->dispatch(
                (string) $this->request->method->name,
                (string) $this->request->url->path,
            );
        $variables = [];

        // presumes a class-string of an invokable class
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $callable = $this->getCallable(Error\RouteNotFound::class);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $callable = $this->getCallable(Error\MethodNotAllowed::class);
                $variables = ['allow' => $routeInfo[1]];
                break;

            default:
                $callable = $this->getCallable($routeInfo[1]);
                $variables = $routeInfo[2];
                break;
        }

        $response = $callable(...$variables);
        return new SapienResponseHandler($response);
    }

    protected function getCallable(string $class) : callable
    {
        /** @var callable */
        return $this->container->get($class);
    }
}
