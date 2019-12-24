<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Query\Group\AllGroupsByUserQuery;
use Tailgate\Domain\Service\Group\AllGroupsByUserQueryHandler;

// get all groups that authenticated user belong to
class AllGroupsByUserAction extends AbstractAction
{   
    private $allGroupsByUserQueryHandler;

    public function __construct(AllGroupsByUserQueryHandler $allGroupsByUserQueryHandler)
    {
        $this->allGroupsByUserQueryHandler = $allGroupsByUserQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        $groups = $this->allGroupsByUserQueryHandler->handle(new AllGroupsByUserQuery($userId));
        return $this->respondWithData($groups);
    }
}