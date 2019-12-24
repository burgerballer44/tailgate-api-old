<?php

namespace TailgateApi\Actions\Team;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Application\Query\Team\TeamQuery;
use Tailgate\Domain\Service\Team\DeleteTeamHandler;
use Tailgate\Domain\Service\Team\TeamQueryHandler;


// delete a team
class DeleteTeamAction extends AbstractAction
{   
    private $deleteTeamHandler;
    private $teamQueryHandler;

    public function __construct(
        DeleteTeamHandler $deleteTeamHandler,
        TeamQueryHandler $teamQueryHandler
    ) {
        $this->deleteTeamHandler = $deleteTeamHandler;
        $this->teamQueryHandler = $teamQueryHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $team = $this->teamQueryHandler->handle(new TeamQuery($teamId));
        $this->deleteTeamHandler->handle(new DeleteTeamCommand($teamId));
        return $this->respond();
    }
}