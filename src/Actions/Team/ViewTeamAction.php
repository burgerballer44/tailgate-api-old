<?php

namespace TailgateApi\Actions\Team;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use TailgateApi\Repository\EventViewRepository;
use Tailgate\Application\Query\Team\TeamQuery;
use Tailgate\Domain\Service\Team\TeamQueryHandler;

// view details of a team
class ViewTeamAction extends AbstractAction
{   
    private $teamQueryHandler;
    private $eventViewRepository;

    public function __construct(
        TeamQueryHandler $teamQueryHandler,
        EventViewRepository $eventViewRepository
    ) {
        $this->teamQueryHandler = $teamQueryHandler;
        $this->eventViewRepository = $eventViewRepository;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $team = $this->teamQueryHandler->handle(new TeamQuery($teamId));
        $team['eventLog'] = $this->eventViewRepository->allById($teamId);
        return $this->respondWithData($team);
    }
}