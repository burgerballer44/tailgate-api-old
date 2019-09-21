<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use TailgateApi\Validators\Group\GroupExist;
use TailgateApi\Validators\Team\TeamExist;

class FollowTeamCommandValidator extends AbstractRespectValidator
{
    private $teamViewRepository;
    private $groupViewRepository;

    public function __construct(
        TeamViewRepositoryInterface $teamViewRepository,
        GroupViewRepositoryInterface $groupViewRepository
    ) {
        $this->teamViewRepository = $teamViewRepository;
        $this->groupViewRepository = $groupViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Team\\");
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['teamId'] = V::notEmpty()->stringType()->TeamExist($this->teamViewRepository)->setName('Team');
        $this->rules['groupId'] = V::notEmpty()->stringType()->GroupExist($this->groupViewRepository)->setName('Group');
    }
}
