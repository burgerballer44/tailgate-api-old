<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use TailgateApi\Validators\Group\GameTimeNotPassedForUpdate;
use TailgateApi\Validators\Group\GroupExist;
use TailgateApi\Validators\Group\ScoreExist;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;

class UpdateScoreForGroupCommandValidator extends AbstractRespectValidator
{
    private $groupViewRepository;
    private $scoreViewRepository;
    private $gameViewRepository;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        ScoreViewRepositoryInterface $scoreViewRepository,
        GameViewRepositoryInterface $gameViewRepository
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->scoreViewRepository = $scoreViewRepository;
        $this->gameViewRepository = $gameViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['groupId'] = V::notEmpty()->GroupExist($this->groupViewRepository)->setName('Group');
        $this->rules['scoreId'] = V::notEmpty()->ScoreExist($this->scoreViewRepository)->GameTimeNotPassedForUpdate($this->scoreViewRepository, $this->gameViewRepository)->setName('Score');
        $this->rules['homeTeamPrediction'] = V::numeric()->intVal()->min(0)->setName('Home Team Prediction');
        $this->rules['awayTeamPrediction'] = V::numeric()->intVal()->min(0)->setName('Away Team Prediction');
    }
}
