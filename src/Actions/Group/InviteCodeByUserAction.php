<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Application\Query\Group\GroupInviteCodeQuery;
use Tailgate\Domain\Service\Group\AddMemberToGroupHandler;
use Tailgate\Domain\Service\Group\GroupInviteCodeQueryHandler;

// authenticated user joins a group by invite code
class InviteCodeByUserAction extends AbstractAction
{   
    private $groupInviteCodeQueryHandler;
    private $addMemberToGroupHandler;

    public function __construct(
        GroupInviteCodeQueryHandler $groupInviteCodeQueryHandler,
        AddMemberToGroupHandler $addMemberToGroupHandler
    ) {
        $this->groupInviteCodeQueryHandler = $groupInviteCodeQueryHandler;
        $this->addMemberToGroupHandler = $addMemberToGroupHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        $parsedBody = $this->request->getParsedBody();
        $inviteCode = $parsedBody['inviteCode'];

        $group = $this->groupInviteCodeQueryHandler->handle(new GroupInviteCodeQuery($inviteCode));
        $groupId = $group['groupId'];

        $command = new AddMemberToGroupCommand($groupId, $userId);
        
        $this->addMemberToGroupHandler->handle($command);
        return $this->respond();
    }
}