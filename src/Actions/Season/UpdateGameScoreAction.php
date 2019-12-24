<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Season\UpdateGameScoreCommand;
use Tailgate\Domain\Service\Season\UpdateGameScoreHandler;

// add a game to a season
class UpdateGameScoreAction extends AbstractAction
{   
    private $updateGameScoreHandler;

    public function __construct(UpdateGameScoreHandler $updateGameScoreHandler)
    {
        $this->updateGameScoreHandler = $updateGameScoreHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();
        
        $command = new UpdateGameScoreCommand(
            $seasonId,
            $gameId,
            $parsedBody['homeTeamScore'] ?? '',
            $parsedBody['awayTeamScore'] ?? '',
            $parsedBody['startDate'] ?? '',
            $parsedBody['startTime'] ?? ''
        );
        
        $this->updateGameScoreHandler->handle($command);
        return $this->respond();
    }
}