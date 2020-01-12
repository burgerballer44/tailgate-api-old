<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use TailgateApi\Validators\Group\GroupExist;
use TailgateApi\Validators\Group\MemberExist;
use TailgateApi\Validators\Group\PlayerExist;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;

class ChangePlayerOwnerCommandValidator extends AbstractRespectValidator
{
    private $groupViewRepository;
    private $playerViewRepository;
    private $memberViewRepository;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        PlayerViewRepositoryInterface $playerViewRepository,
        MemberViewRepositoryInterface $memberViewRepository
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->playerViewRepository = $playerViewRepository;
        $this->memberViewRepository = $memberViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['groupId'] = V::notEmpty()->stringType()->GroupExist($this->groupViewRepository)->setName('Group');
        $this->rules['playerId'] = V::notEmpty()->stringType()->PlayerExist($this->playerViewRepository)->setName('Player');
        $this->rules['memberId'] = V::notEmpty()->stringType()->MemberExist($this->memberViewRepository)->setName('Member');
    }
}
