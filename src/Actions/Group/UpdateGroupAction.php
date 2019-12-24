<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\UpdateGroupCommand;
use Tailgate\Domain\Service\Group\UpdateGroupHandler;

// update a group
class UpdateGroupAction extends AbstractAction
{   
    private $updateGroupHandler;

    public function __construct(UpdateGroupHandler $updateGroupHandler)
    {
        $this->updateGroupHandler = $updateGroupHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new UpdateGroupCommand(
            $groupId,
            $parsedBody['name'],
            $parsedBody['ownerId']
        );

        $this->updateGroupHandler->handle($command);
        
        return $this->respond();
    }
}