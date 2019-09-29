<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Group\Group;
use TailgateApi\Validators\Group\GroupExist;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use TailgateApi\Validators\Group\PlayerExist;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use TailgateApi\Validators\Season\GameExist;

class SubmitScoreForGroupCommandValidator extends AbstractRespectValidator
{
    private $groupViewRepository;
    private $playerViewRepository;
    private $gameViewRepository;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        PlayerViewRepositoryInterface $playerViewRepository,
        GameViewRepositoryInterface $gameViewRepository
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->playerViewRepository = $playerViewRepository;
        $this->gameViewRepository = $gameViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Season\\");
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['groupId'] = V::notEmpty()->GroupExist($this->groupViewRepository)->setName('Group');
        $this->rules['playerId'] = V::notEmpty()->PlayerExist($this->playerViewRepository)->setName('Player');
        $this->rules['gameId'] = V::notEmpty()->GameExist($this->gameViewRepository)->setName('Game');
        $this->rules['homeTeamPrediction'] = V::notEmpty()->intVal()->min(0)->setName('Home Team Prediction');
        $this->rules['awayTeamPrediction'] = V::notEmpty()->intVal()->min(0)->setName('Away Team Prediction');
    }
}