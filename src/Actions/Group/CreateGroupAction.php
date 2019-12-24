<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Domain\Service\Group\CreateGroupHandler;

// create a group, assign user as owner, and member
class CreateGroupAction extends AbstractAction
{   
    private $createGroupHandler;

    public function __construct(CreateGroupHandler $createGroupHandler)
    {
        $this->createGroupHandler = $createGroupHandler;
    }

    public function action() : ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();

        $command = new CreateGroupCommand($parsedBody['name'], $parsedBody['userId']);

        $this->createGroupHandler->handle($command);
        return $this->respond();
    }
}