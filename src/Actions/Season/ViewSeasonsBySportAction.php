<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use TailgateApi\Repository\EventViewRepository;
use Tailgate\Application\Query\Season\AllSeasonsBySportQuery;
use Tailgate\Domain\Service\Season\AllSeasonsBySportQueryHandler;

// view all seasons of a sport
class ViewSeasonsBySportAction extends AbstractAction
{   
    private $allSeasonsBySportQueryHandler;

    public function __construct(AllSeasonsBySportQueryHandler $allSeasonsBySportQueryHandler) {
        $this->allSeasonsBySportQueryHandler = $allSeasonsBySportQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $queryParams = $this->request->getQueryParams();

        $sport = $queryParams['sport'];

        $seasons = $this->allSeasonsBySportQueryHandler->handle(new AllSeasonsBySportQuery($sport));

        return $this->respondWithData($season);
    }
}