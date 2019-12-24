<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeleteFollowCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\DeleteFollowHandler;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;

// delete a follow for group
class DeleteFollowByUserAction extends AbstractAction
{   
    private $groupByUserQueryHandler;
    private $deleteFollowHandler;

    public function __construct(
        GroupByUserQueryHandler $groupByUserQueryHandler,
        DeleteFollowHandler $deleteFollowHandler
    ) {
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
        $this->deleteFollowHandler = $deleteFollowHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);

        // user must be group admin or own the player id
        if ('Group-Admin' != $member['role']) {
            throw new \Exception("Hey! Invalid permissions!");
        }
        
        $command = new DeleteFollowCommand($groupId, $followId);
        $this->deleteFollowHandler->handle($command);
        return $this->respond();
    }
}