<?php

namespace TailgateApi\Actions\Team;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Team\AddTeamCommand;
use Tailgate\Domain\Service\Team\AddTeamHandler;

// add a team
class AddTeamAction extends AbstractAction
{   
    private $addTeamHandler;

    public function __construct(AddTeamHandler $addTeamHandler)
    {
        $this->addTeamHandler = $addTeamHandler;
    }

    public function action() : ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();

        $command = new AddTeamCommand(
            $parsedBody['designation'] ?? '',
            $parsedBody['mascot'] ?? '',
            $parsedBody['sport'] ?? ''
        );

        $this->addTeamHandler->handle($command);
        return $this->respond();
    }
}