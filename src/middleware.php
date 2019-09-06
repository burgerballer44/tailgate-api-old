<?php

use Slim\App;
use TailgateApi\Middleware\JsonBodyParserMiddleware;

return function (App $app) {

    $container = $app->getContainer();

    // Remember LIFO!

    $app->add(new JsonBodyParserMiddleware());

};
