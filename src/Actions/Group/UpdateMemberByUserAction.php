<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;
use Tailgate\Domain\Service\Group\UpdateMemberHandler;

// update a member in a group
class UpdateMemberByUserAction extends AbstractAction
{   
    private $updateMemberHandler;
    private $groupByUserQueryHandler;

    public function __construct(
        UpdateMemberHandler $updateMemberHandler,
        GroupByUserQueryHandler $groupByUserQueryHandler
    ) {
        $this->updateMemberHandler = $updateMemberHandler;
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