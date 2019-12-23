<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use TailgateApi\Repository\EventViewRepository;
use Tailgate\Application\Query\User\UserQuery;
use Tailgate\Domain\Service\User\UserQueryHandler;

// returns user information for authenticated user
class ViewUserAction extends AbstractAction
{   
    private $userQueryHandler;
    private $eventViewRepository;

    public function __construct(
        UserQueryHandler $userQueryHandler,
        EventViewRepository $eventViewRepository
    ) {
        $this->userQueryHandler = $userQueryHandler;
        $this->eventViewRepository = $eventViewRepository;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $user = $this->userQueryHandler->handle(new UserQuery($userId));
        $user['eventLog'] = $this->eventViewRepository->allById($userId);
        return $this->respondWithData($user);
    }
}