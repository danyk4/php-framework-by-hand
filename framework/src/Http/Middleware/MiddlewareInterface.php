<?php

namespace danyk\Framework\Http\Middleware;

use danyk\Framework\Http\Request;
use danyk\Framework\Http\Response;

interface MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response;
}
