<?php

namespace TailgateApi\Actions\Auth;

use OAuth2\Request;
use OAuth2\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// grants a user token to use the api
class TokenAction
{   
    private $oauthRequest;
    private $oauthServer;

    public function __construct(Request $oauthRequest, Server $oauthServer)
    {
        $this->oauthRequest = $oauthRequest;
        $this->oauthServer = $oauthServer;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $serverRequest = $this->oauthRequest->createFromGlobals();
        $serverResponse = $this->oauthServer->handleTokenRequest($serverRequest);
        
        $response = $response->withStatus($serverResponse->getStatusCode());
        foreach ($serverResponse->getHttpHeaders() as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($serverResponse->getResponseBody('json'));
        return $response;
    }
}