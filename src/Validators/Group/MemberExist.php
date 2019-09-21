<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\MemberView;

class MemberExist extends AbstractRule
{
    private $memberViewRepository;

    public function __construct(MemberViewRepositoryInterface $memberViewRepository)
    {
        $this->memberViewRepository = $memberViewRepository;
    }

    /**
     * returns false when the member does not exist
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $memberView = $this->memberViewRepository->get(MemberId::fromString($input));
        } catch (\Throwable $e) {
            $memberView = false;
        }

        return $memberView instanceof MemberView;
    }
}
