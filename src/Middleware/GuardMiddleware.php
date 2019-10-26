<?php

namespace TailgateApi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tailgate\Application\Query\User\UserQuery;

class GuardMiddleware implements MiddlewareInterface
{
    protected $server;
    protected $container;

    public function __construct($server, $container)
    {
        $this->server = $server;
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $server = $this->server;
        $req = \OAuth2\Request::createFromGlobals();

        if (!$server->verifyResourceRequest($req)) {
            $response = $server->getResponse();

            throw new \Exception($response->getStatusText(), $response->getStatusCode());

            // $server->getResponse()->send();
            // exit;
        }

        // store the user_id into the request's attributes
        $token = $server->getAccessTokenData($req);
        $request = $request->withAttribute('userId', $token['user_id']);
        $request = $request->withAttribute('user', $this->container->get('queryBus')->handle(new UserQuery($token['user_id'])));
        
        return $handler->handle($request);
    }
}
