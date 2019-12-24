<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use TailgateApi\Repository\EventViewRepository;
use Tailgate\Application\Query\Season\SeasonQuery;
use Tailgate\Domain\Service\Season\SeasonQueryHandler;

// view details of a season
class ViewSeasonAction extends AbstractAction
{   
    private $seasonQueryHandler;
    private $eventViewRepository;

    public function __construct(
        SeasonQueryHandler $seasonQueryHandler,
        EventViewRepository $eventViewRepository
    ) {
        $this->seasonQueryHandler = $seasonQueryHandler;
        $this->eventViewRepository = $eventViewRepository;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $season = $this->seasonQueryHandler->handle(new SeasonQuery($seasonId));
        $season['eventLog'] = $this->eventViewRepository->allById($seasonId);
        return $this->respondWithData($season);
    }
}