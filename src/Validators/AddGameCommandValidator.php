<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Season\Season;
use TailgateApi\Validators\Season\SeasonExist;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use TailgateApi\Validators\Team\TeamExist;

class AddGameCommandValidator extends AbstractRespectValidator
{
    private $seasonViewRepository;
    private $teamViewRepository;

    public function __construct(
        SeasonViewRepositoryInterface $seasonViewRepository,
        TeamViewRepositoryInterface $teamViewRepository
    ) {
        $this->seasonViewRepository = $seasonViewRepository;
        $this->teamViewRepository = $teamViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Season\\");
        V::with("TailgateApi\Validators\Team\\");

        $this->rules['seasonId'] = V::notEmpty()->SeasonExist($this->seasonViewRepository)->setName('Season');
        $this->rules['homeTeamId'] = V::notEmpty()->TeamExist($this->teamViewRepository)->setName('Home Team');
        $this->rules['awayTeamId'] = V::notEmpty()->TeamExist($this->teamViewRepository)->setName('Away Team');
        $this->rules['startDate'] = V::notEmpty()->date('Y-m-d')->setName('Start Date');
        $this->rules['startTime'] = V::oneOf(V::date('H:i'), V::notEmpty()->stringType()->length(2, 255))->setName('Start Date');
    }
}
