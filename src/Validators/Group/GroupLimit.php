<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

class GroupLimit extends AbstractRule
{
    const GROUP_LIMIT = 100;

    private $memberViewRepository;

    public function __construct(MemberViewRepositoryInterface $memberViewRepository)
    {
        $this->memberViewRepository = $memberViewRepository;
    }

    /**
     * returns false when the user belongs to too many groups
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $memberViews = $this->memberViewRepository->getAllByUser(UserId::fromString($input));
        } catch (\Throwable $e) {
            $memberViews = false;
        }

        return count($memberViews) < self::GROUP_LIMIT;
    }
}
