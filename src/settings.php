<?php

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    $today = (new \DateTime())->format('Y-m-d');

    // all custom settings the app uses should placed here
    $containerBuilder->addDefinitions([
        'settings' => [

            'errorHandlerMiddleware' => [
                'displayErrorDetails' => filter_var(getenv('API_DISPLAY_ERROR_DETAILS'), FILTER_VALIDATE_BOOLEAN),
                'logErrors' => filter_var(getenv('API_LOG_ERRORS'), FILTER_VALIDATE_BOOLEAN),
                'logErrorDetails' => filter_var(getenv('API_LOG_ERRORS'), FILTER_VALIDATE_BOOLEAN),
            ],

            // how long should the token last for in seconds
            'access_lifetime' => 28800, // 8 hours

            // pdo connection to database
            'pdo' => [
                'connection' => getenv('API_DB_CONNECTION'),
                'host' => getenv('API_DB_HOST'),
                'port' => getenv('API_DB_PORT'),
                'database' => getenv('API_DB_DATABASE'),
                'username' => getenv('API_DB_USERNAME'),
                'password' => getenv('API_DB_PASSWORD'),
            ],

            // logger
            'logger' => [
                'name' => 'tailgate-api',
                'path' => __DIR__ . "/../var/logs/app-{$today}.log",
                'level' => Logger::DEBUG,
            ],
        ],
    ]);
};
