<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;

// view a group the user belongs to
class ViewGroupByUserAction extends AbstractAction
{   
    private $groupByUserQueryHandler;

    public function __construct(GroupByUserQueryHandler $groupByUserQueryHandler)
    {
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        return $this->respondWithData($group);
    }
}