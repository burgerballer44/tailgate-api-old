<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;

use Tailgate\Domain\Service\Group\UpdateScoreForGroupHandler;

// update a score
class UpdateScoreAction extends AbstractAction
{   
    private $updateScoreForGroupHandler;

    public function __construct(
        UpdateScoreForGroupHandler $updateScoreForGroupHandler
    ) {
        $this->updateScoreForGroupHandler = $updateScoreForGroupHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new UpdateScoreForGroupCommand(
            $groupId,
            $scoreId,
            $parsedBody['homeTeamPrediction'],
            $parsedBody['awayTeamPrediction']
        );

        $this->updateScoreForGroupHandler->handle($command);

        return $this->respond();
    }
}