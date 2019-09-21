<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupView;

class GroupExist extends AbstractRule
{
    private $groupViewRepository;

    public function __construct(GroupViewRepositoryInterface $groupViewRepository)
    {
        $this->groupViewRepository = $groupViewRepository;
    }

    /**
     * returns false when the group does not exist
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $groupView = $this->groupViewRepository->get(GroupId::fromString($input));
        } catch (\Throwable $e) {
            $groupView = false;
        }

        return $groupView instanceof GroupView;
    }
}
