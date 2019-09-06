<?php

namespace TailgateApi\Controllers;

use GuzzleHttp\Exception\TransferException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

abstract class ApiController
{
    const VALIDATION_ERROR = 'validation_error';

    protected $statusCode = 200;
    protected $container;
    protected $logger;

    /**
     * [__construct description]
     * @param ContainerInterface $container [description]
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->logger = $this->container->get('logger');
    }

    /**
     * [setStatusCode description]
     * @param int $statusCode [description]
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * [respondWithData description]
     * @param  ResponseInterface $response [description]
     * @param  array             $data     [description]
     * @param  array             $headers  [description]
     * @return [type]                      [description]
     */
    protected function respondWithData(ResponseInterface $response, array $data, array $headers = [])
    {
        $response->getBody()->write(json_encode(['data' => $data]));

        foreach ($headers as $headerKey => $headerValue) {
            $response = $response->withAddedHeader($headerKey, $headerValue);
        }
        
        $response->withAddedHeader('Content-Type', 'application/json');
        return $response->withStatus($this->statusCode);
    }

    /**
     * [respondWithError description]
     * @param  ResponseInterface $response [description]
     * @param  string            $type     [description]
     * @param  array             $errors   [description]
     * @return [type]                      [description]
     */
    protected function respondWithError(ResponseInterface $response, string $type, array $errors)
    {
        $data = [
            'code' => $this->statusCode,
            'type' => $type,
            'errors' => $errors,
        ];

        $response->getBody()->write(json_encode($data));
        
        $response->withAddedHeader('Content-Type', 'application/json');
        return $response->withStatus($this->statusCode);
    }

    /**
     * [respondWithValidationError description]
     * @param  ResponseInterface $response [description]
     * @param  array             $errors   [description]
     * @return [type]                      [description]
     */
    protected function respondWithValidationError(ResponseInterface $response, array $errors)
    {
        return $this->setStatusCode(400)->respondWithError($response, self::VALIDATION_ERROR, $errors);
    }
}