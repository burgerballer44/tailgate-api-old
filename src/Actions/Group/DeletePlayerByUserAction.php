<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeletePlayerCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\DeletePlayerHandler;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;

// delete a player from a group
class DeletePlayerByUserAction extends AbstractAction
{   
    private $groupByUserQueryHandler;
    private $deletePlayerHandler;

    public function __construct(
        GroupByUserQueryHandler $groupByUserQueryHandler,
        DeletePlayerHandler $deletePlayerHandler
    ) {
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
        $this->deletePlayerHandler = $deletePlayerHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);
        $playerIds = collect($group['players'])->where('memberId', $member['memberId'])->pluck('playerId')->toArray();

        // user must be group admin or own the player id
        if ('Group-Admin' != $member['role'] && !in_array($playerId, $playerIds)) {
            throw new \Exception("Hey! Invalid permissions!");
        }
        
        $command = new DeletePlayerCommand($groupId, $playerId);
        $this->deletePlayerHandler->handle($command);
        return $this->respond();
    }
}