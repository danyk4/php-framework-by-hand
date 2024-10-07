<?php

namespace danyk\Framework\Routing;

use danyk\Framework\Controller\AbstractController;
use danyk\Framework\Http\Exceptions\MethodNotAllowedException;
use danyk\Framework\Http\Exceptions\RouteNotFoundException;
use danyk\Framework\Http\Request;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use League\Container\Container;

use function FastRoute\simpleDispatcher;

class Router implements RouterInterface
{
    private array $routes;

    public function dispatch(Request $request, Container $container): array
    {
        $handler = $request->getRouteHandler();
        $vars    = $request->getRouteArgs();

        if (is_array($handler)) {
            [$controllerId, $method] = $handler;
            $controller = $container->get($controllerId);

            if (is_subclass_of($controller, AbstractController::class)) {
                $controller->setRequest($request);
            }

            $handler = [$controller, $method];
        }

        $vars['request'] = $request;

        return [$handler, $vars];
    }

}
