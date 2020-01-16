<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use TailgateApi\Repository\EventViewRepository;
use Tailgate\Application\Query\Season\AllFollowedGamesQuery;
use Tailgate\Domain\Service\Season\AllFollowedGamesQueryHandler;

// view all games that a group follows
class AllFollowedGamesAction extends AbstractAction
{   
    private $allFollowedGamesQueryHandler;

    public function __construct(AllFollowedGamesQueryHandler $allFollowedGamesQueryHandler) {
        $this->allFollowedGamesQueryHandler = $allFollowedGamesQueryHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $games = $this->allFollowedGamesQueryHandler->handle(new AllFollowedGamesQuery($followId));
        return $this->respondWithData($games);
    }
}