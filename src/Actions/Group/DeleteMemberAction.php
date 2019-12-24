<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeleteMemberCommand;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Domain\Service\Group\GroupQueryHandler;
use Tailgate\Domain\Service\Group\DeleteMemberHandler;

// update a member in a group
class DeleteMemberAction extends AbstractAction
{   
    private $deleteMemberHandler;
    private $groupQueryHandler;

    public function __construct(
        DeleteMemberHandler $deleteMemberHandler,
        GroupQueryHandler $groupQueryHandler
    ) {
        $this->deleteMemberHandler = $deleteMemberHandler;
        $this->groupQueryHandler = $groupQueryHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();
        $group = $this->groupQueryHandler->handle(new GroupQuery($groupId));
        $this->deleteMemberHandler->handle(new DeleteMemberCommand($groupId, $memberId));
        return $this->respond();
    }
}