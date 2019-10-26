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
    /**
     * register a user as pending
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function registerPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        $command = new RegisterUserCommand(
            $parsedBody['email'] ?? '',
            $parsedBody['password'] ?? '',
            $parsedBody['confirmPassword'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);

        if ($validator->assert($command)) {
            $user = $this->container->get('commandBus')->handle($command);
            return $this->setStatusCode(201)->respondWithData($response, $user);
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    /**
     * will turn a pending user into an active user
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function activate(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);

        // email not used for some reason. not sure why i originally included it.
        // $params = $request->getParsedBody();
        // $email = $params['email'];

        $command = new ActivateUserCommand($userId);

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }
        
        return $this->respondWithValidationError($response, $validator->errors());
    }

    /**
     * returns user information for authenticated user
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function me(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        $user = $this->container->get('queryBus')->handle(new UserQuery($userId));
        return $this->respondWithData($response, $user);
    }

    /**
     * update user information for authenticated user
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function mePatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');

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

    /**
     * update email of authenticated user
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function emailPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');

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

    /**
     * anyone can update their own password
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function passwordPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        $parsedBody = $request->getParsedBody();

        $command = new UpdatePasswordCommand(
            $userId,
            $parsedBody['password'] ?? '',
            $parsedBody['confirmPassword'] ?? ''
        );
        
        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    /**
     * get all users
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function all(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $users =  $this->container->get('queryBus')->handle(new AllUsersQuery());
        return $this->respondWithData($response, $users);
    }

    /**
     * view a user
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function view(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);

        $user = $this->container->get('queryBus')->handle(new UserQuery($userId));
        return $this->respondWithData($response, $user);
    }

    /**
     * update a user
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function userPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
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

    /**
     * delete a user
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $this->container->get('commandBus')->handle(new DeleteUserCommand($userId));
        return $response;
    }
}