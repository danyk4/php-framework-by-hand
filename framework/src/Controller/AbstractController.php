<?php

namespace danyk\Framework\Controller;

use danyk\Framework\Http\Request;
use danyk\Framework\Http\Response;
use Psr\Container\ContainerInterface;
use Twig\Environment;

abstract class AbstractController
{
    protected ?ContainerInterface $container = null;
    protected Request $request;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function render(string $view, array $parameters = [], Response $response = null)
    {
        /** @var Environment $twig */
        $twig = $this->container->get('twig');

        $response ??= new Response();

        $response->setContent($twig->render($view, $parameters));

        return $response;
    }


}
