<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\ChangePlayerOwnerCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\ChangePlayerOwnerHandler;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;

// change the member of a player in a group
class ChangePlayerOwnerByUserAction extends AbstractAction
{   
    private $changePlayerOwnerHandler;
    private $groupByUserQueryHandler;

    public function __construct(
        ChangePlayerOwnerHandler $changePlayerOwnerHandler,
        GroupByUserQueryHandler $groupByUserQueryHandler
    ) {
        $this->changePlayerOwnerHandler = $changePlayerOwnerHandler;
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);

        // user must be a group admin
        if ('Group-Admin' != $member['role']) {
            throw new \Exception("Hey! Invalid permissions!");
        }

        $command = new ChangePlayerOwnerCommand(
            $groupId,
            $playerId,
            $parsedBody['memberId']
        );
        
        $this->changePlayerOwnerHandler->handle($command);
        return $this->respond();
    }
}