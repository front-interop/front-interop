<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use FastRoute\Dispatcher;
use FrontInterop\Example\Error;
use FrontInterop\DelegateInterface;
use FrontInterop\DelegatorInterface;
use Psr\Container\ContainerInterface;
use Sapien\Request;
use Throwable;

class Delegator implements DelegatorInterface
{
    public function __construct(
        protected Request $request,
        protected Dispatcher $dispatcher,
        protected ContainerInterface $container,
    ) {
    }

    public function delegateRequest() : DelegateInterface
    {
        $routeInfo = $this->dispatcher->dispatch(
            $this->request->method->name,
            $this->request->url->path,
        );

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $callable = $this->container->get(Error\RouteNotFoundError::class);
                $arguments = [];
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $callable = $this->container->get(Error\MethodNotAllowedError::class);
                $arguments = [$routeInfo[1]];
                break;

            default:
                $callable = $this->container->get($routeInfo[1]);
                $arguments = $routeInfo[2];
                break;
        }

        return new Delegate($callable, $arguments);
    }

    public function delegateThrowable(Throwable $e) : DelegateInterface
    {
        return new Delegate(
            callable: $this->container->get(Error\ServerError::class),
            arguments: [$e],
        );
    }
}
