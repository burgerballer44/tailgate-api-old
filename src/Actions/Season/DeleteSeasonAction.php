<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Season\DeleteSeasonCommand;
use Tailgate\Application\Query\Season\SeasonQuery;
use Tailgate\Domain\Service\Season\DeleteSeasonHandler;
use Tailgate\Domain\Service\Season\SeasonQueryHandler;


// delete a season
class DeleteSeasonAction extends AbstractAction
{   
    private $deleteSeasonHandler;
    private $seasonQueryHandler;

    public function __construct(
        DeleteSeasonHandler $deleteSeasonHandler,
        SeasonQueryHandler $seasonQueryHandler
    ) {
        $this->deleteSeasonHandler = $deleteSeasonHandler;
        $this->SeasonQueryHandler = $seasonQueryHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $season = $this->SeasonQueryHandler->handle(new SeasonQuery($seasonId));
        $this->deleteSeasonHandler->handle(new DeleteSeasonCommand($seasonId));
        return $this->respond();
    }
}