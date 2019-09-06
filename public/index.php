<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// instantiate PHP-DI Container
$container = new Container();

// set the container we want to use and instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();

// add settings to the app
$settings = require __DIR__ . '/../src/settings.php';
$settings($app);

// configure dependencies the application needs
$dependencies = require __DIR__ . '/../src/dependencies.php';
$dependencies($app);

// set up validation
$validators = require __DIR__ . '/../src/validation.php';
$validators($app);

// register middleware that every request needs
$middleware = require __DIR__ . '/../src/middleware.php';
$middleware($app);

// set up commands
$commands = require __DIR__ . '/../src/commands.php';
$commands($app);

// set up queries
$queries = require __DIR__ . '/../src/queries.php';
$queries($app);

// register routes the application uses
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

// add the final middleware that handles errors
$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
$errorMiddleware = new ErrorMiddleware(
    $callableResolver,
    $responseFactory, 
    $container->get('settings')['displayErrorDetails'],
    false,
    false
);

$app->add($errorMiddleware);

// good luck
$app->run();