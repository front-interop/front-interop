<?php
declare(strict_types=1);

namespace FrontInterop\Example;

use Caplet\Caplet;
use FastRoute;
use FrontInterop\RequestHandler;
use FrontInterop\ResponseHandler;
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
            RequestHandler::class,
            static fn (Caplet $caplet) => $caplet->get(ExampleRequestHandler::class),
        );
        $this->factory(
            FastRoute\Dispatcher::class,
            static fn (Caplet $caplet)
                => FastRoute\simpleDispatcher(
                    static function (FastRoute\RouteCollector $r) {
                $r->addRoute('GET', '/user/{id:\d+}', Action\GetUser::class);
            }),
        );
    }
}
