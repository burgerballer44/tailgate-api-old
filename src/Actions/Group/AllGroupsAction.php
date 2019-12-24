<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Query\Group\AllGroupsQuery;
use Tailgate\Domain\Service\Group\AllGroupsQueryHandler;

// get all groups
class AllGroupsAction extends AbstractAction
{   
    private $allGroupsQueryHandler;

    public function __construct(AllGroupsQueryHandler $allGroupsQueryHandler)
    {
        $this->allGroupsQueryHandler = $allGroupsQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $groups = $this->allGroupsQueryHandler->handle(new AllGroupsQuery());
        return $this->respondWithData($groups);
    }
}