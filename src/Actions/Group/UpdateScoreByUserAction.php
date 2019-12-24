<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;
use Tailgate\Domain\Service\Group\UpdateScoreForGroupHandler;

// update a score
class UpdateScoreByUserAction extends AbstractAction
{   
    private $groupByUserQueryHandler;
    private $updateScoreForGroupHandler;

    public function __construct(
        GroupByUserQueryHandler $groupByUserQueryHandler,
        UpdateScoreForGroupHandler $updateScoreForGroupHandler
    ) {
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
        $this->updateScoreForGroupHandler = $updateScoreForGroupHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);
        $scoreIds = collect($group['scores'])->where('memberId', $member['memberId'])->pluck('scoreId');

        // must be group admin, or the owner of score
        if ('Group-Admin' != $member['role'] && !$scoreIds->contains($scoreId)) {
            throw new \Exception("Hey! Invalid permissions!");
        }

        $command = new UpdateScoreForGroupCommand(
            $groupId,
            $scoreId,
            $parsedBody['homeTeamPrediction'],
            $parsedBody['awayTeamPrediction']
        );

        $this->updateScoreForGroupHandler->handle($command);

        return $this->respond();
    }
}