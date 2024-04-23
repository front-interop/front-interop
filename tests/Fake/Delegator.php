<?php
declare(strict_types=1);

namespace FrontInterop\Fake;

use FastRoute\Dispatcher;
use FrontInterop\DelegateInterface;
use FrontInterop\Impl\Delegate;
use FrontInterop\DelegatorInterface;
use FrontInterop\Fake\Error;
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
            (string) $this->request->method->name,
            (string) $this->request->url->path,
        );

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                /** @var callable */
                $callable = $this->container->get(Error\RouteNotFoundError::class);
                $arguments = [];
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                /** @var callable */
                $callable = $this->container->get(Error\MethodNotAllowedError::class);
                $arguments = [$routeInfo[1]];
                break;

            default:
                /** @var callable */
                $callable = $this->container->get($routeInfo[1]);
                $arguments = $routeInfo[2];
                break;
        }

        return new Delegate($callable, $arguments);
    }

    public function delegateThrowable(Throwable $e) : DelegateInterface
    {
        /** @var callable $callable */
        $callable = $this->container->get(Error\ServerError::class);
        $arguments = [$e];
        return new Delegate($callable, $arguments);
    }
}
