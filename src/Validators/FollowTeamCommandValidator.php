<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use TailgateApi\Validators\Group\GroupExist;
use TailgateApi\Validators\Team\TeamExist;
use TailgateApi\Validators\Team\FollowLimit;

class FollowTeamCommandValidator extends AbstractRespectValidator
{
    private $teamViewRepository;
    private $groupViewRepository;
    private $followViewRepository;

    public function __construct(
        TeamViewRepositoryInterface $teamViewRepository,
        GroupViewRepositoryInterface $groupViewRepository,
        FollowViewRepositoryInterface $followViewRepository
    ) {
        $this->teamViewRepository = $teamViewRepository;
        $this->groupViewRepository = $groupViewRepository;
        $this->followViewRepository = $followViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Team\\");
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['teamId'] = V::notEmpty()->stringType()->TeamExist($this->teamViewRepository)->setName('Team');
        $this->rules['groupId'] = V::notEmpty()->stringType()->GroupExist($this->groupViewRepository)->FollowLimit($this->followViewRepository)->setName('Group');
    }
}
