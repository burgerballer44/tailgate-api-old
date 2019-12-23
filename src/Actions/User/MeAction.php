<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Query\User\UserQuery;
use Tailgate\Domain\Service\User\UserQueryHandler;

// returns user information for authenticated user
class MeAction extends AbstractAction
{   
    private $userQueryHandler;

    public function __construct(UserQueryHandler $userQueryHandler)
    {
        $this->userQueryHandler = $userQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        $user = $this->userQueryHandler->handle(new UserQuery($userId));
        return $this->respondWithData($user);
    }
}