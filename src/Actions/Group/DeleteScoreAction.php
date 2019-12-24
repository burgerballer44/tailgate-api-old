<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Domain\Service\Group\DeleteScoreHandler;
use Tailgate\Domain\Service\Group\GroupQueryHandler;

// delete a score from a group
class DeleteScoreAction extends AbstractAction
{   
    private $groupQueryHandler;
    private $deleteScoreHandler;

    public function __construct(
        GroupQueryHandler $groupQueryHandler,
        DeleteScoreHandler $deleteScoreHandler
    ) {
        $this->groupQueryHandler = $groupQueryHandler;
        $this->deleteScoreHandler = $deleteScoreHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $group = $this->groupQueryHandler->handle(new GroupQuery($groupId));
        $command = new DeleteScoreCommand($groupId, $scoreId);
        $this->deleteScoreHandler->handle($command);
        return $this->respond();
    }
}