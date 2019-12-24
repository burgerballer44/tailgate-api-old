<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeleteMemberCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;
use Tailgate\Domain\Service\Group\DeleteMemberHandler;

// update a member in a group
class DeleteMemberByUserAction extends AbstractAction
{   
    private $deleteMemberHandler;
    private $groupByUserQueryHandler;

    public function __construct(
        DeleteMemberHandler $deleteMemberHandler,
        GroupByUserQueryHandler $groupByUserQueryHandler
    ) {
        $this->deleteMemberHandler = $deleteMemberHandler;
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);

        // user must be a group admin
        if ('Group-Admin' != $member['role']) {
            throw new \Exception("Hey! Invalid permissions!");
        }

        $this->deleteMemberHandler->handle(new DeleteMemberCommand($groupId, $memberId));
        return $this->respond();
    }
}