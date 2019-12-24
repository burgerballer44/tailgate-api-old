<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Domain\Service\Group\SubmitScoreForGroupHandler;

// submit a score for a game in a group by a player
class SubmitScoreAction extends AbstractAction
{   
    private $submitScoreForGroupHandler;

    public function __construct(SubmitScoreForGroupHandler $submitScoreForGroupHandler)
    {
        $this->submitScoreForGroupHandler = $submitScoreForGroupHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new SubmitScoreForGroupCommand(
           $groupId,
           $playerId,
           $parsedBody['gameId'],
           $parsedBody['homeTeamPrediction'],
           $parsedBody['awayTeamPrediction']
        );

        $this->submitScoreForGroupHandler->handle($command);

        return $this->respond();
    }
}