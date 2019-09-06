<?php

namespace TailgateApi\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class JsonBodyParserMiddleware
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request = $request->withParsedBody($contents);
            }
        }

        return $handler->handle($request);
    }
}