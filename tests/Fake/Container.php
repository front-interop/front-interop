<?php
declare(strict_types=1);

namespace FrontInterop\Fake;

use Caplet\Caplet;
use FastRoute;
use FrontInterop\DelegatorInterface;
use FrontInterop\Fake\Delegator;
use FrontInterop\Impl\RequestHandler;
use FrontInterop\RequestHandlerInterface;
use Psr\Container\ContainerInterface;

class Container extends Caplet
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->factory(
            ContainerInterface::class,
            fn (Caplet $caplet) => $caplet,
        );
        $this->factory(
            RequestHandlerInterface::class,
            fn (Caplet $caplet) => $caplet->get(RequestHandler::class),
        );
        $this->factory(
            DelegatorInterface::class,
            fn (Caplet $caplet) => $caplet->get(Delegator::class),
        );
        $this->factory(
            FastRoute\Dispatcher::class,
            function (Caplet $caplet) {
                $routes = function (FastRoute\RouteCollector $r) {
                    $r->addRoute('GET', '/user/{id:\d+}', Action\GetUserAction::class);
                };
                return FastRoute\simpleDispatcher($routes);
            },
        );
    }
}
