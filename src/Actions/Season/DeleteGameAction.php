<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Season\DeleteGameCommand;
use Tailgate\Application\Query\Season\SeasonQuery;
use Tailgate\Domain\Service\Season\DeleteGameHandler;
use Tailgate\Domain\Service\Season\SeasonQueryHandler;

// delete a game
class DeleteGameAction extends AbstractAction
{   
    private $deleteGameHandler;
    private $seasonQueryHandler;

    public function __construct(
        DeleteGameHandler $deleteGameHandler,
        SeasonQueryHandler $seasonQueryHandler
    ) {
        $this->deleteGameHandler = $deleteGameHandler;
        $this->seasonQueryHandler = $seasonQueryHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $season = $this->seasonQueryHandler->handle(new SeasonQuery($seasonId));
        $this->deleteGameHandler->handle(new DeleteGameCommand($seasonId, $gameId));
        return $this->respond();
    }
}