<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use TailgateApi\Validators\Team\TeamExist;

class UpdateTeamCommandValidator extends AbstractRespectValidator
{
    private $teamViewRepository;

    public function __construct(TeamViewRepositoryInterface $teamViewRepository)
    {
        $this->teamViewRepository = $teamViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Team\\");

        $this->rules['teamId'] = V::notEmpty()->stringType()->TeamExist($this->teamViewRepository)->setName('Team');
        $this->rules['designation'] = V::notEmpty()->stringType()->length(2, 100)->setName('Designation');
        $this->rules['mascot'] = V::notEmpty()->stringType()->length(2, 50)->setName('Mascot');
    }
}
