<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\User\ActivateUserCommand;
use Tailgate\Domain\Service\User\ActivateUserHandler;

// will turn a pending user into an active user
class ActivateUserAction extends AbstractAction
{   
    private $activateUserHandler;

    public function __construct(ActivateUserHandler $activateUserHandler)
    {
        $this->activateUserHandler = $activateUserHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);

        // email not used for some reason. not sure why i originally included it.
        // $params = $request->getParsedBody();
        // $email = $params['email'];

        $command = new ActivateUserCommand($userId);

        $this->activateUserHandler->handle($command);

        return $this->respond();
    }
}