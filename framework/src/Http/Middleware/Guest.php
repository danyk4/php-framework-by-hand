<?php

namespace danyk\Framework\Http\Middleware;

use danyk\Framework\Authentication\SessionAuthInterface;
use danyk\Framework\Http\Middleware\MiddlewareInterface;
use danyk\Framework\Http\RedirectResponse;
use danyk\Framework\Http\Request;
use danyk\Framework\Http\Response;
use danyk\Framework\Session\SessionInterface;

class Guest implements MiddlewareInterface
{
    public function __construct(
        private SessionAuthInterface $auth,
        private SessionInterface $session,
    ) {
    }


    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->session->start();

        if ($this->auth->check()) {
            return new RedirectResponse('/dashboard');
        }

        return $handler->handle($request);
    }
}
