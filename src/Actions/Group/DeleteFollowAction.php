<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeleteFollowCommand;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Domain\Service\Group\DeleteFollowHandler;
use Tailgate\Domain\Service\Group\GroupQueryHandler;

// delete a follow for group
class DeleteFollowAction extends AbstractAction
{   
    private $groupQueryHandler;
    private $deleteFollowHandler;

    public function __construct(
        GroupQueryHandler $groupQueryHandler,
        DeleteFollowHandler $deleteFollowHandler
    ) {
        $this->groupQueryHandler = $groupQueryHandler;
        $this->deleteFollowHandler = $deleteFollowHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $group = $this->groupQueryHandler->handle(new GroupQuery($groupId));
        $command = new DeleteFollowCommand($groupId, $followId);
        $this->deleteFollowHandler->handle($command);
        return $this->respond();
    }
}