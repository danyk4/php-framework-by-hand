<?php

namespace App\Controllers;

use App\Services\YouTubeService;
use danyk\Framework\Http\Response;

class HomeController
{
  public function __construct(
    private readonly YouTubeService $youTube
  ) {
  }

  public function index(): Response
  {
    $content = '<h1>Content from HomeController</h1>';
    $content .= "<a href=\"{$this->youTube->getChannelUrl()}\">YouTube</a>";

    return new Response($content);
  }
}
