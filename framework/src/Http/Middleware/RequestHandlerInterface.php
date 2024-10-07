<?php

namespace danyk\Framework\Http\Middleware;

use danyk\Framework\Http\Request;
use danyk\Framework\Http\Response;

interface RequestHandlerInterface
{
    public function handle(Request $request): Response;

    public function injectMiddleware(array $middleware): void;
}
