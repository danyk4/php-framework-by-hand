<?php

namespace danyk\Framework\Http\Events;

use danyk\Framework\Event\Event;
use danyk\Framework\Http\Request;
use danyk\Framework\Http\Response;

class ResponseEvent extends Event
{
    public function __construct(
        private readonly Request $request,
        private readonly Response $response
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
