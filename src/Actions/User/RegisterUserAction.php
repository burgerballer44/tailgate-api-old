<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\User\RegisterUserCommand;
use Tailgate\Domain\Service\User\RegisterUserHandler;

// register a new user and mark as pending
class RegisterUserAction extends AbstractAction
{   
    private $registerUserHandler;

    public function __construct(RegisterUserHandler $registerUserHandler)
    {
        $this->registerUserHandler = $registerUserHandler;
    }

    public function action() : ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();

        $command = new RegisterUserCommand(
            $parsedBody['email'] ?? '',
            $parsedBody['password'] ?? '',
            $parsedBody['confirmPassword'] ?? ''
        );

        $user = $this->registerUserHandler->handle($command);

        return $this->respondWithData($user, 201);
    }
}