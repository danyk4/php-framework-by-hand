<?php

namespace danyk\Framework\Http;

use danyk\Framework\Event\EventDispatcher;
use danyk\Framework\Http\Events\ResponseEvent;
use danyk\Framework\Http\Exceptions\HttpException;
use danyk\Framework\Http\Middleware\RequestHandlerInterface;
use League\Container\Container;

class Kernel
{
    private string $appEnv = 'local';

    public function __construct(
        public readonly Container $container,
        private readonly RequestHandlerInterface $requestHandler,
        private readonly EventDispatcher $eventDispatcher,
    ) {
        $this->appEnv = $this->container->get('APP_ENV');
    }

    public function handle(Request $request): Response
    {
        try {
            $response = $this->requestHandler->handle($request);
        } catch (\Exception $e) {
            $response = $this->createExceptionResponse($e);
        }

        $response->setStatusCode(500);

        $this->eventDispatcher->dispatch(new ResponseEvent($request, $response));

        return $response;
    }

    private function createExceptionResponse(\Exception $e): Response
    {
        if (in_array($this->appEnv, ['local', 'testing'])) {
            throw $e;
        }
        if ($e instanceof HttpException) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }

        return new Response('Server error', 500);
    }

    public function terminate(Request $request, Response $response): void
    {
        $request->getSession()?->clearFlash();
    }
}
