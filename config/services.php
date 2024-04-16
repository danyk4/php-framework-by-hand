<?php

use danyk\Framework\Http\Kernel;
use danyk\Framework\Routing\Router;
use danyk\Framework\Routing\RouterInterface;
use League\Container\Container;

$container = new Container();

$container->add(RouterInterface::class, Router::class);

$container->add(Kernel::class)
          ->addArgument(RouterInterface::class);

return $container;
