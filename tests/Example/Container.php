<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use Caplet\Caplet;
use FastRoute;
use FrontInterop\RequestHandlerInterface;
use FrontInterop\DelegatorInterface;
use Psr\Container\ContainerInterface;

class Container extends Caplet
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->factory(
            ContainerInterface::class,
            static fn (Caplet $caplet) => $caplet,
        );

        $this->factory(
            RequestHandlerInterface::class,
            static fn (Caplet $caplet) => $caplet->get(RequestHandler::class),
        );

        $this->factory(
            DelegatorInterface::class,
            static fn (Caplet $caplet) => $caplet->get(Delegator::class),
        );

        $this->factory(
            FastRoute\Dispatcher::class,
            static fn (Caplet $caplet)
                => FastRoute\simpleDispatcher(
                    static function (FastRoute\RouteCollector $r
                ) {
                    $r->addRoute('GET', '/user/{id:\d+}', Action\GetUserAction::class);
                }
            ),
        );
    }
}
