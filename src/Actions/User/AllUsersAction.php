<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Query\User\AllUsersQuery;
use Tailgate\Domain\Service\User\AllUsersQueryHandler;

// get all users
class AllUsersAction extends AbstractAction
{   
    private $allUsersQueryHandler;

    public function __construct(AllUsersQueryHandler $allUsersQueryHandler)
    {
        $this->allUsersQueryHandler = $allUsersQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $users = $this->allUsersQueryHandler->handle(new AllUsersQuery());
        return $this->respondWithData($users);
    }
}