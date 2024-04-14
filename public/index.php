<?php

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH .'/vendor/autoload.php';

use danyk\Framework\Http\Kernel;
use danyk\Framework\Http\Request;
use danyk\Framework\Routing\Router;

$request = Request::createFromGlobals();

$router = new Router();


$kernel = new Kernel($router);
$response = $kernel->handle($request);

$response->send();

// route
// {domain}/posts/{1} -> handle()
