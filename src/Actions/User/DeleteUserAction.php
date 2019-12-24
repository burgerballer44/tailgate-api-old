<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\User\DeleteUserCommand;
use Tailgate\Application\Query\User\UserQuery;
use Tailgate\Domain\Service\User\DeleteUserHandler;
use Tailgate\Domain\Service\User\UserQueryHandler;

// delete a user
class DeleteUserAction extends AbstractAction
{   
    private $deleteUserHandler;
    private $userQueryHandler;

    public function __construct(
        DeleteUserHandler $deleteUserHandler,
        UserQueryHandler $userQueryHandler
    ) {
        $this->deleteUserHandler = $deleteUserHandler;
        $this->userQueryHandler = $userQueryHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $user = $this->userQueryHandler->handle(new UserQuery($userId));
        $this->deleteUserHandler->handle(new DeleteUserCommand($userId));
        return $this->respond();
    }
}