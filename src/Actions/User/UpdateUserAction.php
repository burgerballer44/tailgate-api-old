<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\User\UpdateUserCommand;
use Tailgate\Domain\Service\User\UpdateUserHandler;

// update a user
class UpdateUserAction extends AbstractAction
{   
    private $updateUserHandler;

    public function __construct(UpdateUserHandler $updateUserHandler)
    {
        $this->updateUserHandler = $updateUserHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new UpdateUserCommand(
            $userId,
            $parsedBody['email'] ?? '',
            $parsedBody['status'] ?? '',
            $parsedBody['role'] ?? ''
        );

        $this->updateUserHandler->handle($command);

        return $this->respond();
    }
}