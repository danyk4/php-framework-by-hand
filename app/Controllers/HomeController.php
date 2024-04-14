<?php

namespace App\Controllers;

use danyk\Framework\Http\Response;

class HomeController
{
  public function index(): Response
  {
    $content = '<h1>Content from Kernel</h1>';

    return new Response($content);
  }
}
