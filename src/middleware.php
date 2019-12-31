<?php

use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Error\Renderers\JsonErrorRenderer;
use Slim\Middleware\ErrorMiddleware;
use TailgateApi\Handlers\MyErrorHandler;
use TailgateApi\Middleware\EventSubscribersMiddleware;
use TailgateApi\Middleware\JsonBodyParserMiddleware;
use TailgateApi\Middleware\ValidationExceptionMiddleware;

return function (App $app) {

    $container = $app->getContainer();

    // Remember LIFO!
    // last in this list is the first touched

    $app->add(EventSubscribersMiddleware::class);
    $app->add(JsonBodyParserMiddleware::class);
    $app->add(ValidationExceptionMiddleware::class);

    // add error middleware last
    $settings = $container->get('settings')['errorHandlerMiddleware'];
    $displayErrorDetails = $settings['displayErrorDetails'];
    $logErrors = $settings['logErrors'];
    $logErrorDetails = $settings['logErrorDetails'];

    $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);

    // use my custom error handler
    $myErrorHandler = new MyErrorHandler(
        $app->getCallableResolver(),
        $app->getResponseFactory(),
        $container->get(LoggerInterface::class)
    );
    $errorMiddleware->setDefaultErrorHandler($myErrorHandler);

    // set json as default error handling
    $errorHandler = $errorMiddleware->getDefaultErrorHandler();
    $errorHandler->setDefaultErrorRenderer('application/json', JsonErrorRenderer::class);

    $app->add($errorMiddleware);
};
