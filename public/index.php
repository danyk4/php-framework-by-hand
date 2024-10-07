<?php

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH.'/vendor/autoload.php';

use danyk\Framework\Http\Kernel;
use danyk\Framework\Http\Request;

$request = Request::createFromGlobals();

/* @var \League\Container\Container $container */
$container = require BASE_PATH.'/config/services.php';

require_once BASE_PATH.'/bootstrap/bootstrap.php';

$kernel = $container->get(Kernel::class);

$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);

