<?php

namespace TailgateApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends ApiController
{
    /**
     * [token description]
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function token(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $serverRequest = \OAuth2\Request::createFromGlobals();
        $serverResponse = $this->container->get('oauthServer')->handleTokenRequest($serverRequest);
       
        $response = $response->withStatus($serverResponse->getStatusCode());
        foreach ($serverResponse->getHttpHeaders() as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($serverResponse->getResponseBody('json'));
        return $response;
    }

}