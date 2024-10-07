<?php

namespace danyk\Framework\Http\Middleware;

use danyk\Framework\Http\Middleware\RequestHandlerInterface;
use danyk\Framework\Http\Request;
use danyk\Framework\Http\Response;
use Psr\Container\ContainerInterface;

class RequestHandler implements RequestHandlerInterface
{
    private array $middleware = [
        ExtractRouteInfo::class,
        StartSession::class,
        RouterDispatch::class,
    ];

    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    public function handle(Request $request): Response
    {
        // If there are no middleware classes return default respnose
        // Response have to be returned before the list will become empty
        if (empty($this->middleware)) {
            return new Response('Server Error', 500);
        }

        // Get next middleware class for executing
        $middlewareClass = array_shift($this->middleware);

        // Create new call of middleware process

        $middleware = $this->container->get($middlewareClass);

        $response = $middleware->process($request, $this);

        return $response;
    }

    public function injectMiddleware(array $middleware): void
    {
        array_splice($this->middleware, 0, 0, $middleware);
    }
}
