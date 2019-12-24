<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Domain\Service\Group\UpdateMemberHandler;

// update a member in a group
class UpdateMemberAction extends AbstractAction
{   
    private $updateMemberHandler;

    public function __construct(UpdateMemberHandler $updateMemberHandler)
    {
        $this->updateMemberHandler = $updateMemberHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new UpdateMemberCommand(
            $groupId,
            $memberId,
            $parsedBody['groupRole'],
            $parsedBody['allowMultiple'],
        );
        
        $this->updateMemberHandler->handle($command);
        return $this->respond();
    }
}