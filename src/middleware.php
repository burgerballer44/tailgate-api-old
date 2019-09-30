<?php

use Slim\App;
use TailgateApi\Middleware\JsonBodyParserMiddleware;

return function (App $app) {

    $container = $app->getContainer();

    // Remember LIFO!
    // last in this list is the first touched

    $app->add(new JsonBodyParserMiddleware());

};
