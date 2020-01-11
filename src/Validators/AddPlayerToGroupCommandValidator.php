<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use TailgateApi\Validators\Group\GroupExist;
use TailgateApi\Validators\Group\MemberExist;
use TailgateApi\Validators\Group\UniqueUsernamePerGroup;

class AddPlayerToGroupCommandValidator extends AbstractRespectValidator
{
    private $groupViewRepository;
    private $memberViewRepository;
    private $playerViewRepository;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        MemberViewRepositoryInterface $memberViewRepository,
        PlayerViewRepositoryInterface $playerViewRepository
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->memberViewRepository = $memberViewRepository;
        $this->playerViewRepository = $playerViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['groupId'] = V::notEmpty()->stringType()->GroupExist($this->groupViewRepository)->setName('Group');
        $this->rules['memberId'] = V::notEmpty()->stringType()->MemberExist($this->memberViewRepository)->setName('Member');
        $this->rules['username'] = V::notEmpty()->stringType()->UniqueUsernamePerGroup($this->playerViewRepository, $command->getGroupId())->noWhitespace()->alnum()->length(2, 20)->setName('Username');
    }
}
