<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\DeleteScoreHandler;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;

// delete a score from a group
class DeleteScoreByUserAction extends AbstractAction
{   
    private $groupByUserQueryHandler;
    private $deleteScoreHandler;

    public function __construct(
        GroupByUserQueryHandler $groupByUserQueryHandler,
        DeleteScoreHandler $deleteScoreHandler
    ) {
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
        $this->deleteScoreHandler = $deleteScoreHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);
        $scoreIds = collect($group['scores'])->where('memberId', $member['memberId'])->pluck('scoreId');

        // must group admin, or the owner of score
        if ('Group-Admin' != $member['role'] && !$scoreIds->contains($scoreId)) {
            throw new \Exception("Hey! Invalid permissions!");
        }
        
        $command = new DeleteScoreCommand($groupId, $scoreId);
        $this->deleteScoreHandler->handle($command);
        return $this->respond();
    }
}