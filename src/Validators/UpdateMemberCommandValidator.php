<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use TailgateApi\Validators\Group\GroupExist;
use TailgateApi\Validators\Group\MemberExist;
use Tailgate\Domain\Model\Group\Group;

class UpdateMemberCommandValidator extends AbstractRespectValidator
{
    private $memberViewRepository;
    private $groupViewRepository;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        MemberViewRepositoryInterface $memberViewRepository
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->memberViewRepository = $memberViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\User\\");
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['groupId'] = V::notEmpty()->stringType()->GroupExist($this->groupViewRepository)->setName('Group');
        $this->rules['memberId'] = V::notEmpty()->stringType()->MemberExist($this->memberViewRepository)->setName('Owner');
        $this->rules['groupRole'] = V::notEmpty()->in(Group::getValidGroupRoles())->setName('Group Role');
        $this->rules['allowMultiplePlayers'] = V::in([0,1])->setName('Allow Multiple');
    }
}
