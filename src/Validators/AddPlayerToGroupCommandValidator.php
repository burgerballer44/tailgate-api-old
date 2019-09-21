<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use TailgateApi\Validators\Group\GroupExist;
use TailgateApi\Validators\Group\MemberExist;

class AddPlayerToGroupCommandValidator extends AbstractRespectValidator
{
    private $groupViewRepository;
    private $memberViewRepository;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        MemberViewRepositoryInterface $memberViewRepository
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->memberViewRepository = $memberViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['groupId'] = V::notEmpty()->stringType()->GroupExist($this->groupViewRepository)->setName('Group');
        $this->rules['memberId'] = V::notEmpty()->stringType()->MemberExist($this->memberViewRepository)->setName('Member');
        $this->rules['username'] = V::notEmpty()->stringType()->noWhitespace()->alnum()->length(4, 20)->setName('Username');
    }
}
