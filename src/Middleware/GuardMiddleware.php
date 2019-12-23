<?php

namespace TailgateApi\Middleware;

use OAuth2\Request;
use OAuth2\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tailgate\Application\Query\User\UserQuery;
use Tailgate\Domain\Service\User\UserQueryHandler;

class GuardMiddleware implements MiddlewareInterface
{
    protected $oauthRequest;
    protected $oauthServer;
    protected $userQueryHandler;

    public function __construct(Request $oauthRequest, Server $oauthServer, UserQueryHandler $userQueryHandler)
    {
        $this->oauthRequest = $oauthRequest;
        $this->oauthServer = $oauthServer;
        $this->userQueryHandler = $userQueryHandler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $server = $this->oauthServer;
        $req = $this->oauthRequest->createFromGlobals();

        if (!$server->verifyResourceRequest($req)) {
            $response = $server->getResponse();

            throw new \Exception($response->getStatusText(), $response->getStatusCode());
        }

        // store the user_id into the request's attributes
        $token = $server->getAccessTokenData($req);
        $request = $request->withAttribute('userId', $token['user_id']);
        $request = $request->withAttribute('user', $this->userQueryHandler->handle(new UserQuery($token['user_id'])));
        
        return $handler->handle($request);
    }
}
