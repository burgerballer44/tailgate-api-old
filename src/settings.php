<?php

use Monolog\Logger;
use Slim\App;

return function (App $app) {

    $container = $app->getContainer();

    $today = (new \DateTime())->format('Y-m-d');

    // all custom settings the app uses should placed here
    $container->set('settings', [

        // should errors be displayed
        'displayErrorDetails' => filter_var(getenv('DISPLAY_ERROR_DETAILS'), FILTER_VALIDATE_BOOLEAN),

        // how long should the token last for in seconds
        'access_lifetime' => 28800, // 8 hours

        // pdo connection to database
        'pdo' => [
            'connection' => getenv('DB_CONNECTION'),
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
        ],

        // logger
        'logger' => [
            'name' => 'tailgate-api',
            'path' => __DIR__ . "/../var/logs/app-{$today}.log",
            'level' => Logger::DEBUG,
        ],

    ]);
};
