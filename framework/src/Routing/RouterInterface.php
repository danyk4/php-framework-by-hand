<?php

namespace danyk\Framework\Routing;

use danyk\Framework\Http\Request;
use League\Container\Container;

interface RouterInterface
{
    public function dispatch(Request $request, Container $container);
}
