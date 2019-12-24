<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Domain\Service\Group\AddPlayerToGroupHandler;

// add a player to group
class AddPlayerAction extends AbstractAction
{   
    private $addPlayerToGroupHandler;

    public function __construct(AddPlayerToGroupHandler $addPlayerToGroupHandler)
    {
        $this->addPlayerToGroupHandler = $addPlayerToGroupHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new AddPlayerToGroupCommand(
            $groupId,
            $memberId,
            $parsedBody['username'] ?? ''
        );
        
        $this->addPlayerToGroupHandler->handle($command);
        return $this->respond();
    }
}