<?php

namespace danyk\Framework\Http\Middleware;

use danyk\Framework\Http\Middleware\MiddlewareInterface;
use danyk\Framework\Http\Request;
use danyk\Framework\Http\Response;
use danyk\Framework\Routing\RouterInterface;
use Psr\Container\ContainerInterface;

class RouterDispatch implements MiddlewareInterface
{
    public function __construct(
        private RouterInterface $router,
        private ContainerInterface $container
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        [$routeHandler, $vars] = $this->router->dispatch($request, $this->container);

        $response = call_user_func_array($routeHandler, array_values($vars));

        return $response;
    }
}
