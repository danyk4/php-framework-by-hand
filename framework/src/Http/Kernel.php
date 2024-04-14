<?php

namespace danyk\Framework\Http;

use danyk\Framework\Http\Exceptions\HttpException;
use danyk\Framework\Routing\RouterInterface;

class Kernel
{
  public function __construct(
    private RouterInterface $router
  ) {
  }

  public function handle(Request $request): Response
  {
    try {
      [$routeHandler, $vars] = $this->router->dispatch($request);

      $reponse = call_user_func_array($routeHandler, $vars);
    } catch (HttpException $e) {
      $reponse = new Response($e->getMessage(), $e->getStatusCode());
    } catch (\Throwable $e) {
      $reponse = new Response($e->getMessage(), 500 );
    }

    return $reponse;
  }
}
