<?php

namespace TailgateApi\Actions\Group;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use TailgateApi\Repository\EventViewRepository;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Domain\Service\Group\GroupQueryHandler;

// view a group
class ViewGroupAction extends AbstractAction
{   
    private $groupQueryHandler;
    private $eventViewRepository;

    public function __construct(
        GroupQueryHandler $groupQueryHandler,
        EventViewRepository $eventViewRepository
    ) {
        $this->groupQueryHandler = $groupQueryHandler;
        $this->eventViewRepository = $eventViewRepository;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $group = $this->groupQueryHandler->handle(new GroupQuery($groupId));
        $group['eventLog'] = $this->eventViewRepository->allById($groupId);
        return $this->respondWithData($group);
    }
}