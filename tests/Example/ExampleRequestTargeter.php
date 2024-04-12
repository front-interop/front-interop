<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use FastRoute\Dispatcher;
use FrontInterop\Example\Error;
use FrontInterop\RequestTarget;
use FrontInterop\RequestTargeter;
use Psr\Container\ContainerInterface;
use Sapien\Request;

class ExampleRequestTargeter implements RequestTargeter
{
    public function __construct(
        protected Request $request,
        protected Dispatcher $dispatcher,
        protected ContainerInterface $container,
    ) {
    }

    public function getRequestTarget() : RequestTarget
    {
        $routeInfo = $this->dispatcher->dispatch(
            $this->request->method->name,
            $this->request->url->path,
        );

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $callable = $this->container->get(Error\RouteNotFound::class);
                $arguments = [];
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $callable = $this->container->get(Error\MethodNotAllowed::class);
                $arguments = [$routeInfo[1]];
                break;

            default:
                $callable = $this->container->get($routeInfo[1]);
                $arguments = $routeInfo[2];
                break;
        }

        return new ExampleRequestTarget($callable, $arguments);
    }
}
