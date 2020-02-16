<?php

namespace TailgateApi\Actions\Team;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use TailgateApi\Repository\EventViewRepository;
use Tailgate\Application\Query\Team\TeamsBySportQuery;
use Tailgate\Domain\Service\Team\TeamsBySportQueryHandler;

// view all teams associated to a sport
class ViewTeamsBySportAction extends AbstractAction
{   
    private $teamsBySportQueryHandler;

    public function __construct(TeamsBySportQueryHandler $teamsBySportQueryHandler)
    {
        $this->teamsBySportQueryHandler = $teamsBySportQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $queryParams = $this->request->getQueryParams();

        $sport = $queryParams['sport'];

        $teams = $this->teamsBySportQueryHandler->handle(new TeamsBySportQuery($sport));

        return $this->respondWithData($teams);
    }
}