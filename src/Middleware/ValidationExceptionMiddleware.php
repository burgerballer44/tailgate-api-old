<?php

namespace TailgateApi\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tailgate\Application\Validator\ValidationException;

/**
 * A JSON validation exception middleware.
 */
final class ValidationExceptionMiddleware implements MiddlewareInterface
{

    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $exception) {
            $response = $this->responseFactory->createResponse()->withStatus(400)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['errors' => $exception->getValidationErrors()]));
            return $response;
        }
    }
}
