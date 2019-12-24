<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;
use Tailgate\Domain\Service\Group\SubmitScoreForGroupHandler;

// submit a score for a game in a group by a player
class SubmitScoreByUserAction extends AbstractAction
{   
    private $groupByUserQueryHandler;
    private $submitScoreForGroupHandler;

    public function __construct(
        GroupByUserQueryHandler $groupByUserQueryHandler,
        SubmitScoreForGroupHandler $submitScoreForGroupHandler
    ) {
        $this->groupByUserQueryHandler = $groupByUserQueryHandler;
        $this->submitScoreForGroupHandler = $submitScoreForGroupHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();
        $group = $this->groupByUserQueryHandler->handle(new GroupByUserQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);
        $playersIds = collect($group['players'])->where('memberId', $member['memberId'])->pluck('playerId');

        // must be group admin, or the owner of player
        if ('Group-Admin' != $member['role'] && !$playersIds->contains($playerId)) {
           throw new \Exception("Hey! Invalid permissions!");
        }

        $command = new SubmitScoreForGroupCommand(
           $groupId,
           $playerId,
           $parsedBody['gameId'],
           $parsedBody['homeTeamPrediction'],
           $parsedBody['awayTeamPrediction']
        );

        $this->submitScoreForGroupHandler->handle($command);

        return $this->respond();
    }
}