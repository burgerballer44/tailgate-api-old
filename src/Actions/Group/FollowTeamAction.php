<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\FollowTeamCommand;
use Tailgate\Domain\Service\Group\FollowTeamHandler;

// group follows a team
class FollowTeamAction extends AbstractAction
{   
    private $followTeamHandler;

    public function __construct(FollowTeamHandler $followTeamHandler)
    {
        $this->followTeamHandler = $followTeamHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new FollowTeamCommand(
            $groupId,
            $parsedBody['teamId'] ?? '',
            $parsedBody['seasonId'] ?? ''
        );

        $this->followTeamHandler->handle($command);
        return $this->respond();
    }
}