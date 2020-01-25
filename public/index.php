<?php

use DI\ContainerBuilder;
use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

// set environment variables
require __DIR__ . '/../src/environment.php';

// instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// in production environments cache the container
if (PROD_MODE) {
    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache/container');
}

// add settings to the container
$settings = require __DIR__ . '/../src/settings.php';
$settings($containerBuilder);

// configure dependencies in the container the application needs
(require __DIR__ . '/../src/dependencies.php')($containerBuilder);

// set up validation
(require __DIR__ . '/../src/validation.php')($containerBuilder);

// build PHP-DI Container instance
$container = $containerBuilder->build();

// create app instance
$app = $container->get(App::class);

// register middleware that every request needs
(require __DIR__ . '/../src/middleware.php')($app);

// register routes the application uses
(require __DIR__ . '/../src/routes.php')($app);

// run app
$app->run();