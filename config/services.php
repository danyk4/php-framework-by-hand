<?php

use danyk\Framework\Console\Application;
use danyk\Framework\Console\Commands\MigrateCommand;
use danyk\Framework\Console\Kernel as ConsoleKernel;
use danyk\Framework\Controller\AbstractController;
use danyk\Framework\Dbal\ConnectionFactory;
use danyk\Framework\Http\Kernel;
use danyk\Framework\Routing\Router;
use danyk\Framework\Routing\RouterInterface;
use Doctrine\DBAL\Connection;
use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$dotenv = new Dotenv();
$dotenv->load(BASE_PATH.'/.env');

// Application parameters

$routes      = include BASE_PATH.'/routes/web.php';
$appEnv      = $_ENV['APP_ENV'] ?? 'local';
$viewsPath   = BASE_PATH.'/views';
$databaseUrl = 'pdo-mysql://root:root@mysql_db:3306/db?charset=utf8mb4';


// Application services

$container = new Container();

$container->delegate(new ReflectionContainer(true));

$container->add('framework-commands-namespace', new StringArgument('danyk\\Framework\\Console\\Commands\\'));


$container->add('APP_ENV', new StringArgument($appEnv));

$container->add(RouterInterface::class, Router::class);

$container->extend(RouterInterface::class)
          ->addMethodCall('registerRoutes', [new ArrayArgument($routes)]);

$container->add(Kernel::class)
          ->addArgument(RouterInterface::class)
          ->addArgument($container);

$container->addShared('twig-loader', FilesystemLoader::class)
          ->addArgument(new StringArgument($viewsPath));

$container->addShared('twig', Environment::class)
          ->addArgument('twig-loader');

$container->inflector(AbstractController::class)
          ->invokeMethod('setContainer', [$container]);

$container->add(ConnectionFactory::class)
          ->addArgument(new StringArgument($databaseUrl));

$container->addShared(Connection::class, function () use ($container): Connection {
    return $container->get(ConnectionFactory::class)->create();
});

$container->add(Application::class)
          ->addArgument($container);

$container->add(ConsoleKernel::class)
          ->addArgument($container)
          ->addArgument(Application::class);

$container->add('console:migrate', MigrateCommand::class)
          ->addArgument(Connection::class)
          ->addArgument(new StringArgument(BASE_PATH.'/database/migrations'));

return $container;
