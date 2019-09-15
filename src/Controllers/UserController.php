<?php

namespace TailgateApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;

use Tailgate\Application\Query\User\UserQuery;
use Tailgate\Application\Query\User\AllUsersQuery;
use Tailgate\Application\Command\User\ActivateUserCommand;
use Tailgate\Application\Command\User\DeleteUserCommand;
use Tailgate\Application\Command\User\RegisterUserCommand;
use Tailgate\Application\Command\User\UpdateEmailCommand;
use Tailgate\Application\Command\User\UpdatePasswordCommand;
use Tailgate\Application\Command\User\UpdateUserCommand;

class UserController extends ApiController
{   
    // admin - can see all users
    public function all(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $users =  $this->container->get('queryBus')->handle(new AllUsersQuery());
        return $this->respondWithData($response, $users);
    }

    // admin - can view any user
    // regular - view their own data
    public function view(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $args['userId'];
        $user = $this->container->get('queryBus')->handle(new UserQuery($userId));
        return $this->respondWithData($response, $user);
    }
    
    // anyone
    public function registerPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        $command = new RegisterUserCommand(
            $parsedBody['email'] ?? '',
            $parsedBody['password'] ?? '',
            $parsedBody['confirm_password'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);

        if ($validator->assert($command)) {
            $user = $this->container->get('commandBus')->handle($command);
            return $this->setStatusCode(201)->respondWithData($response, $user);
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    // anyone can activate their account
    public function activate(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $args['userId'];
        $params = $request->getParsedBody();
        $email = $params['email'];

        $command = new ActivateUserCommand($userId);

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }
        
        return $this->respondWithValidationError($response, $validator->errors());
    }

    // developer
    public function userPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $args['userId'];
        $parsedBody = $request->getParsedBody();

        $command = new UpdateUserCommand(
            $userId,
            $parsedBody['email'] ?? '',
            $parsedBody['status'] ?? '',
            $parsedBody['role'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }
        
        return $this->respondWithValidationError($response, $validator->errors());
    }

    // developer
    public function delete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $args['userId'];
        $this->container->get('commandBus')->handle(new DeleteUserCommand($userId));
        return $response;
    }

    // anyone can update their own email
    public function emailPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $args['userId'];
        $parsedBody = $request->getParsedBody();

        $command = new UpdateEmailCommand(
            $userId,
            $parsedBody['email'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }
        
        return $this->respondWithValidationError($response, $validator->errors());
    }

    // anyone can update their own password
    public function passwordPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $args['userId'];
        $parsedBody = $request->getParsedBody();

        $command = new UpdatePasswordCommand(
            $userId,
            $parsedBody['password'] ?? '',
            $parsedBody['confirm_password'] ?? ''
        );
        
        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }
}