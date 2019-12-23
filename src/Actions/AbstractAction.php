<?php

namespace TailgateApi\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractAction
{
    protected $request;
    protected $response;
    protected $args;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        return $this->action();
    }

    abstract protected function action(): ResponseInterface;

    protected function respond()
    {
        return $this->response;
    }

    protected function respondWithData(array $data = [], int $code = 200)
    {
        $this->response->getBody()->write(json_encode(['data' => $data], JSON_PRETTY_PRINT));
        $this->response = $this->response->withHeader('Content-Type', 'application/json');
        return $this->response->withStatus($code);
    }
}
