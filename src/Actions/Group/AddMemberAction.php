<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Domain\Service\Group\AddMemberToGroupHandler;

// add user to a group to become a member
class AddMemberAction extends AbstractAction
{   
    private $addMemberToGroupHandler;

    public function __construct(AddMemberToGroupHandler $addMemberToGroupHandler)
    {
        $this->addMemberToGroupHandler = $addMemberToGroupHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new AddMemberToGroupCommand(
            $groupId,
            $parsedBody['userId'] ?? ''
        );

        $this->addMemberToGroupHandler->handle($command);
        return $this->respond();
    }
}