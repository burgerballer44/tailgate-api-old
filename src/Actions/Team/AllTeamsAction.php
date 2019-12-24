<?php

namespace TailgateApi\Actions\Team;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Query\Team\AllTeamsQuery;
use Tailgate\Domain\Service\Team\AllTeamsQueryHandler;

// view all teams
class AllTeamsAction extends AbstractAction
{   
    private $allTeamsQueryHandler;

    public function __construct(AllTeamsQueryHandler $allTeamsQueryHandler)
    {
        $this->allTeamsQueryHandler = $allTeamsQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $teams = $this->allTeamsQueryHandler->handle(new AllTeamsQuery());
        return $this->respondWithData($teams);
    }
}