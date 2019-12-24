<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\AddPlayerToGroupHandler;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;

// add a player to group
class AddPlayerByUserAction extends AbstractAction
{   
    private $groupByUserQueryHandler;
    private $addPlayerToGroupHandler;

    public function __construct(
        GroupByUserQueryHandler $groupByUserQueryHandler,
        AddPlayerToGroupHandler $addPlayerToGroupHandler
    ) {
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
        $this->addPlayerToGroupHandler = $addPlayerToGroupHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);

        // user must be group admin or the user themself
        if ('Group-Admin' != $member['role'] && $member['memberId'] != $memberId) {
            throw new \Exception("Hey! Invalid permissions!");
        }

        $parsedBody = $this->request->getParsedBody();

        $command = new AddPlayerToGroupCommand(
            $groupId,
            $memberId,
            $parsedBody['username'] ?? ''
        );
        
        $this->addPlayerToGroupHandler->handle($command);
        return $this->respond();
    }
}