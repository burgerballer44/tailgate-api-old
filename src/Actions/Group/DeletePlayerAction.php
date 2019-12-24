<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeletePlayerCommand;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Domain\Service\Group\DeletePlayerHandler;
use Tailgate\Domain\Service\Group\GroupQueryHandler;

// delete a player from a group
class DeletePlayerAction extends AbstractAction
{   
    private $groupQueryHandler;
    private $deletePlayerHandler;

    public function __construct(
        GroupQueryHandler $groupQueryHandler,
        DeletePlayerHandler $deletePlayerHandler
    ) {
        $this->groupQueryHandler = $groupQueryHandler;
        $this->deletePlayerHandler = $deletePlayerHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $group = $this->groupQueryHandler->handle(new GroupQuery($groupId));
        $command = new DeletePlayerCommand($groupId, $playerId);
        $this->deletePlayerHandler->handle($command);
        return $this->respond();
    }
}