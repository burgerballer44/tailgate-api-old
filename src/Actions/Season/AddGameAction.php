<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Season\AddGameCommand;
use Tailgate\Domain\Service\Season\AddGameHandler;

// add a game to a season
class AddGameAction extends AbstractAction
{   
    private $addGameHandler;

    public function __construct(AddGameHandler $addGameHandler)
    {
        $this->addGameHandler = $addGameHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new AddGameCommand(
            $seasonId,
            $parsedBody['homeTeamId'] ?? '',
            $parsedBody['awayTeamId'] ?? '',
            $parsedBody['startDate'] ?? '',
            $parsedBody['startTime'] ?? ''
        );
        
        $this->addGameHandler->handle($command);
        return $this->respond();
    }
}