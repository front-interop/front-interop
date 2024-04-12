# `front-interop`

The `front-interop` project defines a set of interoperable interfaces for the [FrontController pattern](https://martinfowler.com/eaaCatalog/frontController.html) in PHP.

The following two interfaces define the request-receiving and response-sending behaviors at the outer boundary of your HTTP presentation layer:

- `RequestHandler::handleRequest() : ResponseHandler` encapsulates the logic to receive an incoming HTTP request and return the logic to send or emit an HTTP response.

- `ResponseHandler::handleResponse() : void` encapsulates the logic to send or emit an outgoing response.

Further, this interface the process for determining which logic will fulfill the request and return a response:

- `RequestTargeter::getRequestTarget() : RequestTarget` encapsulates the logic to determine which controller, action, middleware stack, or other element will process the request.

The encapsulated request-processing logic is entirely undefined by `front-interop`, and may use a router, middleware, Model-View-Controller presentation, Action-Domain-Responder presentation, or any other combination of components and collaborations.

## Background

The outer boundary of presentation logic in PHP frameworks tends to follow the same order of events: do some setup work, then create and invoke logic to process a request, and finally they send the resulting response.

The relevant Laravel [`public/index.php`](https://github.com/laravel/laravel/blob/10.x/public/index.php) code:

```php
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();
```

The Slim [`App::run()`](https://github.com/slimphp/Slim/blob/4.x/Slim/App.php) method:

```php
public function run(?ServerRequestInterface $request = null): void
{
    if (!$request) {
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        $request = $serverRequestCreator->createServerRequestFromGlobals();
    }

    $response = $this->handle($request);
    $responseEmitter = new ResponseEmitter();
    $responseEmitter->emit($response);
}
```

The relevant Symfony [Create Your Own PHP Framework](https://symfony.com/doc/current/create_framework/dependency_injection.html) `web/front.php` code:

```php
$container = include __DIR__.'/../src/container.php';
$request = Request::createFromGlobals();
$response = $container->get('framework')->handle($request);
$response->send();
```

The relevant Kevin Smith no-framework [`public/index.php`](https://github.com/kevinsmith/no-framework/blob/master/public/index.php) code:

```php
/** @noinspection PhpUnhandledExceptionInspection */
$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
/** @noinspection PhpVoidFunctionResultUsedInspection */
return $emitter->emit($response);
```

These systems are all very different internally, but their outer boundary logic is remarkably similar, as is that logic in many other systems.

## Problem, Part 1: Request and Response

Each of the above examples uses different request/response libraries. Laravel and Symfony use the Symfony HttpFoundation library, whereas Slim and the no-framework use the PSR-7 interfaces. Likewise, other frameworks may use some other library.

This raises a problem for interoperability, because the request and response objects are passed into and out of the front controller logic directly as method arguments and return values. No typehint can cover all the different possibilities, thus preventing interoperability between the different front controller implementations.

### Solution: _RequestHandler_ and _ResponseHandler_

The interoperability solution to this problem is twofold:

1. Instead of passing the request object as a method argument, move the request *creation* logic into the request *handling* logic, whether as a constructor parameter fulfilled by dependency injection, or via a request factory, or some other approach.

2. Instead of returning the response object directly, return a response *sending* object that encapsulates the response.

These two very minor changes allow for a pair of interfaces that are interoperable across a wide range of systems. These interfaces are completely independent of any particular request/response library; that is, they will work with PSR-7, Symfony HttpFoundation, Sapien, or any other request/response library.

### Example

Using the above interfaces, the outer boundary logic at a `public/index.php` bootstrap might look like this:

```php
use FrontInterop\RequestHandler;
use FrontInterop\ResponseHandler;
use Psr\Container\ContainerInterface;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var ContainerInterface */
$container = require dirname(__DIR__) . '/config/container.php';

/** @var RequestHandler */
$requestHandler = $container->get(RequestHandler::class);

/** @var ResponseHandler */
$responseHandler = $requestHandler->handleRequest();
$responseHandler->handleResponse();
```

The _RequestHandler_ and _ResponseHandler_ can now use any request and response objects they like via dependency injection, along with any router, middleware, or other libraries they need to pass a request to a controller or action and get back a response.

A condensed variation of the bootstrap might look like this:

```php
use FrontInterop\RequestHandler;
use Psr\Container\ContainerInterface;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var ContainerInterface */
$container = require dirname(__DIR__) . '/config/container.php';

$container
    ->get(RequestHandler::class)
    ->handleRequest()
    ->handleResponse();
```

Aside from non-container setup, that would be the entire outer boundary code at `public/index.php`.

### _RequestHandler_ Implementation

This [FastRouteRequestHandler.php](./tests/Example/FastRouteRequestHandler.php) implementation uses [FastRoute](https://github.com/nikic/FastRoute) and callable action objects to process a Sapien request.

Note that the implementation does not return a Sapien response object directly; instead, it returns that response composed into an _ExampleSapienResponseHandler_ implementation.

The _RequestHandler_ implementation could be completely replaced by one that uses any combination of router, middleware dispatcher, controller or action invocation, and request/response objects, without changing any of the `public/index.php` bootstrap logic.

### _ResponseHandler_ Implementation

Likewise, the _ResponseHandler_ can encapsulate any response object and implement the appropriate response-sending logic. The `front-interop` project provides _ResponseHandler_ implementations for these response objects ...

- [PSR-7](./tests/Example/ExamplePsrResponseHandler.php)
- [Sapien](./tests/Example/ExampleSapienResponseHandler.php)
- [Symfony](./tests/Example/ExampleSymfonyResponseHandler.php)

... though of course consumers can write any implementation they desire.

The _ResponseHandler_ implementation could be completely replaced without changing any of the bootstrap logic above.

## Problem, Part 2: Routing and Middleware

The _RequestHandler_ must direct the incoming request to some target logic that will fulfill the request and return a response. Typically this is achieved via ...

- a router subsystem that picks a controller method or action class to build a response; or,
- a middleware subsystem that processes the request on the way in and returns a response on the way out.

The problem is that these subsystems are not themselves compatible. For example, different routers define routes in different ways, and adhere to no common specification. Likewise, different middleware subsystems may use different middleware signatures.

The _RequestHandler_ itself might manage the routing or middleware subsystem directly, as in the above example. However, by delegating the concern of subsystem management to a separate element, different subsystem implementations may be swapped out, leaving the rest of the _RequestHandler_ logic unchanged.

### Solution

The solution is to care *not* about the routing or middleware subsystems per se, but *instead* about **what is to be invoked as a result** of the subsystem operations. That is, to care about the *target* for request processing chosen by that subsytem. This _RequestTarget_ is composed of:

- a callable, such as a controller object method, an invokable action object, or a middleware stack; and,
- the arguments to pass to that callable, typically derived from the incoming request.

The _RequestTargeter_ may use a routing system internally to determine the route, then use the route information to build and return a _RequestTarget_. Alternatively, the targeter may just return a middleware stack as the _RequestTarget_.

The _RequestHandler_ then invokes the _RequestTarget_ callable and arguments to get back a response, and wraps that response in an appropriate _ResponseHandler_.

### _RequestTargeter_ Implementation

This [ExampleRequestTargeter.php](./tests/Example/ExampleRequestTargeter.php) extracts the routing and object creation logic from the [FastRouteRequestHandler.php](./tests/Example/FastRouteRequestHandler.php).

As a result, this revised [TargeterRequestHandler.php](./tests/Example/TargeterRequestHandler.php) can use any targeter subsystem, allowing a complete replacement of the router implementation with any other implementation, or even with any middleware implementation.

This means the _RequestHandler_ no longer needs to know how to *choose* the logic to fulfill the request; it only knows how to *invoke* that logic to get back a response.

## Components and Collaborations

- `index.php` calls _RequestHandler_
    - _RequestHandler_ calls _RequestTargeter_
        - _RequestTargeter_ returns a _RequestTarget_
    - _RequestHandler_ invokes the identified _RequestTarget_ logic
        - That logic returns a response
    - _RequestHandler_ returns a _ResponseHandler_ with that response
- `index.php` invokes _ResponseHandler_ to send the response


## Prior Art

The [Laminas Mezzio](https://docs.mezzio.dev/mezzio/) project makes allowances for different DI containers, templating, routers, and error handlers, but requires PSR-7 at the outer boundary. As such, it does not achieve the interoperability goal of `front-interop`.
