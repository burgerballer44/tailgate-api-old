<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeleteGroupCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\DeleteGroupHandler;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;


// delete a group
class DeleteGroupByUserAction extends AbstractAction
{   
    private $deleteGroupHandler;
    private $groupByUserQueryHandler;

    public function __construct(
        DeleteGroupHandler $deleteGroupHandler,
        GroupByUserQueryHandler $groupByUserQueryHandler
    ) {
        $this->deleteGroupHandler = $deleteGroupHandler;
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));

        // user must be the group owner
        if ($userId != $group['ownerId']) {
            throw new \Exception("Hey! Invalid permissions!");
        }

        $this->deleteGroupHandler->handle(new DeleteGroupCommand($groupId));
        return $this->respond();
    }
}