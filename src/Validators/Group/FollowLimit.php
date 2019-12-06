<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\FollowView;

class FollowLimit extends AbstractRule
{
    const FOLLOW_LIMIT = 1;

    private $followViewRepository;

    public function __construct(FollowViewRepositoryInterface $followViewRepository)
    {
        $this->followViewRepository = $followViewRepository;
    }

    /**
     * group should only follow one team at a time
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {
        try {
            $followView = $this->followViewRepository->getByGroup(GroupId::fromString($input));
        } catch (\Throwable $e) {
            $followView = null;
        }

        return !$followView instanceof FollowView;
    }
}
