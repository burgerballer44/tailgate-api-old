<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Group\DeleteGroupCommand;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Domain\Service\Group\DeleteGroupHandler;
use Tailgate\Domain\Service\Group\GroupQueryHandler;


// delete a group
class DeleteGroupAction extends AbstractAction
{   
    private $deleteGroupHandler;
    private $groupQueryHandler;

    public function __construct(
        DeleteGroupHandler $deleteGroupHandler,
        GroupQueryHandler $groupQueryHandler
    ) {
        $this->deleteGroupHandler = $deleteGroupHandler;
        $this->groupQueryHandler = $groupQueryHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $group = $this->groupQueryHandler->handle(new GroupQuery($groupId));
        $this->deleteGroupHandler->handle(new DeleteGroupCommand($groupId));
        return $this->respond();
    }
}