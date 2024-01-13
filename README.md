# `front-interop`

The `front-interop` project defines a set of interoperable interfaces for the [FrontController pattern](https://martinfowler.com/eaaCatalog/frontController.html) in PHP. These interfaces define the request-receiving and response-sending behaviors at the outermost boundary of your HTTP presentation layer:

- `RequestHandler::handleRequest() : ResponseHandler` encapsulates the logic to transform an incoming HTTP request to an outgoing HTTP response. This encapsulated logic is entirely undefined by `front-interop`, and may use a router, middleware, Model-View-Controller presentation, Action-Domain-Responder presentation, or any other combination of components and collaborations.

- `ResponseHandler::handleResponse() : void` encapsulates the logic to send or emit an outgoing response.

## Background

The outer boundary of presentation logic in PHP frameworks tend to follow the same order of events: they do some setup work, then create and invoke logic to process a request, and finally they send the resulting response.

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

## Problem

Each of the above examples uses different request/response libraries. Laravel and Symfony use the Symfony HttpFoundation library, whereas Slim and the no-framework use the PSR-7 interfaces. Likewise, any other framework may use some other library.

This raises a problem for interoperability, because the request and response objects are passed into and out of the front controller logic directly as method arguments and return values. No typehint can cover all the different possibilities, thus preventing interoperability between the different front controller implementations.

## Solution

The interoperability solution to this problem is twofold:

1. Instead of passing the request object as a method argument, move the request *creation* logic into the request *handling* logic, whether as a constructor parameter fulfilled by dependency injection, or via a request factory, or some other approach.

2. Instead of returning the response object directly, return a response *sending* object that encapsulates the response.

These two very minor changes allow for a pair of interfaces that are interoperable across a wide range of systems. These interfaces are completely independent of any particular request/response library; that is, they will work with PSR-7, Symfony HttpFoundation, Sapien, or any other request/response library.

## Example

Using the above interfaces, the outer boundary logic at a `public/index.php` bootstrap might look like this:

```php
use FrontInterop\RequestHandler;
use FrontInterop\ResponseHandler;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface */
$container = require '/path/to/container.php';

/** @var RequestHandler */
$requestHandler = $container->get(RequestHandler::class);

/** @var ResponseHandler */
$responseHandler = $requestHandler->handleRequest();
$responseHandler->handleResponse();
```

The _RequestHandler_ and _ResponseHandler_ can now use any request and response objects they like via dependency injection, along with any router, middleware, or other libraries they need to pass a request to a controller or action and get back a response.

## _RequestHandler_ Implementation

This [ExampleRequestHandler.php](./tests/Example/ExampleRequestHandler.php) implementation uses [FastRoute](https://github.com/nikic/FastRoute) and callable route handlers to process a Sapien request.

Note that the implementation does not return a Sapien response object directly; instead, it returns that response composed into a _ResponseHandler_ implementation.

The _RequestHandler_ implementation could be completely replaced by one that uses any combination of router, middleware dispatcher, controller or action invocation, and request/response objects, without changing any of the bootstrap logic above.

## _ResponseHandler_ Implementation

Likewise, the _ResponseHandler_ can encapsulate any response object and implement the appropriate response-sending logic. The `front-interop` project provides _ResponseHandler_ implementations for these response objects ...

- [PSR-7](./src/ResponseHandler/PsrResponseHandler.php)
- [Sapien](./src/ResponseHandler/SapienResponseHandler.php)
- [Symfony](./src/ResponseHandler/SymfonyResponseHandler.php)

... though of course consumers can write any replacement implementation they choose.

The _ResponseHandler_ implementation could be completely replaced without changing any of the bootstrap logic above.

## Prior Art

The [Laminas Mezzio](https://docs.mezzio.dev/mezzio/) project makes allowances for different DI containers, templating, routers, and error handlers, but requires PSR-7 at the outer boundary. As such, it does not achieve the interoperability goal of `front-interop`.
