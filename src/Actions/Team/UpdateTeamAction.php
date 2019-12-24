<?php

namespace TailgateApi\Actions\Team;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Team\UpdateTeamCommand;
use Tailgate\Domain\Service\Team\UpdateTeamHandler;

// update a team
class UpdateTeamAction extends AbstractAction
{   
    private $updateTeamHandler;

    public function __construct(UpdateTeamHandler $updateTeamHandler)
    {
        $this->updateTeamHandler = $updateTeamHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new UpdateTeamCommand(
            $teamId,
            $parsedBody['designation'] ?? '',
            $parsedBody['mascot'] ?? ''
        );

        $this->updateTeamHandler->handle($command);

        return $this->respond();
    }
}