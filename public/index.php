<?php

use DI\Container;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// set environment variables
require __DIR__ . '/../src/environment.php';

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

// register routes the application uses
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

// run app
$app->run();