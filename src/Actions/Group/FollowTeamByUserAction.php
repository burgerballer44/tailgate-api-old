<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\FollowTeamCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\FollowTeamHandler;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;


// group follows a team
class FollowTeamByUserAction extends AbstractAction
{   
    private $groupByUserQueryHandler;
    private $followTeamHandler;

    public function __construct(
        GroupByUserQueryHandler $groupByUserQueryHandler,
        FollowTeamHandler $followTeamHandler
    ) {
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
        $this->followTeamHandler = $followTeamHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);

        // user must be group admin
        if ('Group-Admin' != $member['role']) {
            throw new \Exception("Hey! Invalid permissions!");
        }

        $command = new FollowTeamCommand(
            $groupId,
            $parsedBody['teamId'] ?? '',
            $parsedBody['seasonId'] ?? ''
        );

        $this->followTeamHandler->handle($command);
        return $this->respond();
    }
}