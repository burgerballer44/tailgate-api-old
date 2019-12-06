<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use TailgateApi\Validators\Group\GroupExist;
use TailgateApi\Validators\Team\TeamExist;
use TailgateApi\Validators\Season\SeasonExist;
use TailgateApi\Validators\Season\SeasonNotEnded;
use TailgateApi\Validators\Group\FollowLimit;

class FollowTeamCommandValidator extends AbstractRespectValidator
{
    private $teamViewRepository;
    private $groupViewRepository;
    private $followViewRepository;
    private $seasonViewRepository;

    public function __construct(
        TeamViewRepositoryInterface $teamViewRepository,
        GroupViewRepositoryInterface $groupViewRepository,
        FollowViewRepositoryInterface $followViewRepository,
        SeasonViewRepositoryInterface $seasonViewRepository
    ) {
        $this->teamViewRepository = $teamViewRepository;
        $this->groupViewRepository = $groupViewRepository;
        $this->followViewRepository = $followViewRepository;
        $this->seasonViewRepository = $seasonViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Team\\");
        V::with("TailgateApi\Validators\Group\\");
        V::with("TailgateApi\Validators\Season\\");

        $this->rules['seasonId'] = V::notEmpty()->stringType()->SeasonExist($this->seasonViewRepository)->SeasonNotEnded($this->seasonViewRepository)->setName('Season');
        $this->rules['teamId'] = V::notEmpty()->stringType()->TeamExist($this->teamViewRepository)->setName('Team');
        $this->rules['groupId'] = V::notEmpty()->stringType()->GroupExist($this->groupViewRepository)->FollowLimit($this->followViewRepository)->setName('Group');
    }
}
