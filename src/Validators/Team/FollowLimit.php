<?php

namespace TailgateApi\Validators\Team;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Team\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupId;

class FollowLimit extends AbstractRule
{
    const FOLLOW_LIMIT = 1;

    private $followViewRepository;

    public function __construct(FollowViewRepositoryInterface $followViewRepository)
    {
        $this->followViewRepository = $followViewRepository;
    }

    /**
     * returns false when the group follows too many teams
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $followViews = $this->followViewRepository->getAllByGroup(GroupId::fromString($input));
        } catch (\Throwable $e) {
            $followViews = false;
        }

        return count($followViews) < self::FOLLOW_LIMIT;
    }
}
