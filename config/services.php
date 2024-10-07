<?php

use App\Services\UserService;
use danyk\Framework\Authentication\SessionAuthentication;
use danyk\Framework\Authentication\SessionAuthInterface;
use danyk\Framework\Console\Application;
use danyk\Framework\Console\Commands\MigrateCommand;
use danyk\Framework\Console\Kernel as ConsoleKernel;
use danyk\Framework\Controller\AbstractController;
use danyk\Framework\Dbal\ConnectionFactory;
use danyk\Framework\Event\EventDispatcher;
use danyk\Framework\Http\Kernel;
use danyk\Framework\Http\Middleware\ExtractRouteInfo;
use danyk\Framework\Http\Middleware\RequestHandler;
use danyk\Framework\Http\Middleware\RequestHandlerInterface;
use danyk\Framework\Http\Middleware\RouterDispatch;
use danyk\Framework\Routing\Router;
use danyk\Framework\Routing\RouterInterface;
use danyk\Framework\Session\Session;
use danyk\Framework\Session\SessionInterface;
use danyk\Framework\Template\TwigFactory;
use Doctrine\DBAL\Connection;
use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dotenv = new Dotenv;
$dotenv->load(dirname(__DIR__).'/.env');

// Application parameters
$basePath    = dirname(__DIR__);
$routes      = include $basePath.'/routes/web.php';
$appEnv      = $_ENV['APP_ENV'] ?? 'local';
$viewsPath   = $basePath.'/views';
$databaseUrl = 'pdo-mysql://root:root@mysql_db:3306/db?charset=utf8mb4';

// Application services

$container = new Container;

$container->add('base-path', new StringArgument($basePath));

$container->delegate(new ReflectionContainer(true));

$container->add('framework-commands-namespace', new StringArgument('danyk\\Framework\\Console\\Commands\\'));

$container->add('APP_ENV', new StringArgument($appEnv));

$container->add(RouterInterface::class, Router::class);

//$container->extend(RouterInterface::class)
//          ->addMethodCall('registerRoutes', [new ArrayArgument($routes)]);

$container->add(RequestHandlerInterface::class, RequestHandler::class)
          ->addArgument($container);

$container->addShared(EventDispatcher::class);

$container->add(Kernel::class)
          ->addArguments([
              $container,
              RequestHandlerInterface::class,
              EventDispatcher::class,
          ]);

//$container->addShared('twig-loader', FilesystemLoader::class)
//          ->addArgument(new StringArgument($viewsPath));
//
//$container->addShared('twig', Environment::class)
//          ->addArgument('twig-loader');

$container->addShared(SessionInterface::class, Session::class);

$container->add('twig-factory', TwigFactory::class)
          ->addArguments([
              new StringArgument($viewsPath),
              SessionInterface::class,
              SessionAuthInterface::class,
          ]);

$container->addShared('twig', function () use ($container) {
    return $container->get('twig-factory')->create();
});

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
          ->addArgument(new StringArgument($basePath.'/database/migrations'));

$container->add(RouterDispatch::class)
          ->addArguments([
              RouterInterface::class,
              $container,
          ]);

$container->add(SessionAuthInterface::class, SessionAuthentication::class)
          ->addArguments([
              UserService::class,
              SessionInterface::class,
          ]);

$container->add(ExtractRouteInfo::class)
          ->addArgument(new ArrayArgument($routes));

return $container;
