<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Domain\Service\Group\CreateGroupHandler;

// create a group, assign authenticed user as owner if not admin, and member
class CreateGroupByUserAction extends AbstractAction
{   
    private $createGroupHandler;

    public function __construct(CreateGroupHandler $createGroupHandler)
    {
        $this->createGroupHandler = $createGroupHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        $parsedBody = $this->request->getParsedBody();

        $command = new CreateGroupCommand($parsedBody['name'], $userId);

        $this->createGroupHandler->handle($command);
        return $this->respond();
    }
}