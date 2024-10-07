<?php

namespace danyk\Framework\Http\Middleware;

use danyk\Framework\Http\Middleware\MiddlewareInterface;
use danyk\Framework\Http\Request;
use danyk\Framework\Http\Response;
use danyk\Framework\Session\SessionInterface;

class StartSession implements MiddlewareInterface
{
    public function __construct(
        private SessionInterface $session,
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->session->start();

        $request->setSession($this->session);

        return $handler->handle($request);
    }
}
