<?php

namespace TailgateApi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TailgateApi\Transactions\TransactionHandlerInterface;

class TransactionMiddleware implements MiddlewareInterface
{
    private $transactionHandler;

    public function __construct(TransactionHandlerInterface $transactionHandler)
    {
        $this->transactionHandler = $transactionHandler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $operation = function () use ($request, $handler) {
            return $handler->handle($request);
        };

        return $this->transactionHandler->execute($operation);
    }
}
