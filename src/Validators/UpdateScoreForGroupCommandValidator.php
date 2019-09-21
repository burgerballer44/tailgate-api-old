<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Group\Group;
use TailgateApi\Validators\Group\GroupExist;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use TailgateApi\Validators\Group\ScoreExist;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;

class UpdateScoreForGroupCommandValidator extends AbstractRespectValidator
{
    private $groupViewRepository;
    private $scoreViewRepository;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        ScoreViewRepositoryInterface $scoreViewRepository
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->scoreViewRepository = $scoreViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['groupId'] = V::notEmpty()->GroupExist($this->groupViewRepository)->setName('Group');
        $this->rules['scoreId'] = V::notEmpty()->ScoreExist($this->scoreViewRepository)->setName('Score');
        $this->rules['homeTeamPrediction'] = V::notEmpty()->intVal()->min(0)->setName('Home Team Prediction');
        $this->rules['awayTeamPrediction'] = V::notEmpty()->intVal()->min(0)->setName('Away Team Prediction');
    }
}
