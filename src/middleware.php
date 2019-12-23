<?php

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use TailgateApi\Middleware\JsonBodyParserMiddleware;
use TailgateApi\Middleware\ValidationExceptionMiddleware;

return function (App $app) {

    $container = $app->getContainer();

    // Remember LIFO!
    // last in this list is the first touched

    $app->add(JsonBodyParserMiddleware::class);
    $app->add(ValidationExceptionMiddleware::class);

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

};
