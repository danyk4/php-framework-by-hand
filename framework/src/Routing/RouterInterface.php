<?php

namespace danyk\Framework\Routing;

use danyk\Framework\Http\Request;

interface RouterInterface
{
  public function dispatch(Request $request);
}
