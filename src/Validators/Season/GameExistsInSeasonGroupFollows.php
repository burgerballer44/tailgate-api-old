<?php

namespace TailgateApi\Validators\Season;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\GameView;

class GameExistsInSeasonGroupFollows extends AbstractRule
{
    private $gameViewRepository;
    private $followViewRepository;
    private $groupId;

    public function __construct(
        GameViewRepositoryInterface $gameViewRepository,
        FollowViewRepositoryInterface $followViewRepository,
        $groupId
    ) {
        $this->gameViewRepository = $gameViewRepository;
        $this->followViewRepository = $followViewRepository;
        $this->groupId = $groupId;
    }

    /**
     * returns false when the Game does not exist in the season that is being followed by the group
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $gameView = $this->gameViewRepository->get(GameId::fromString($input));
            $followView = $this->followViewRepository->getByGroup(GroupId::fromString($this->groupId));
        } catch (\Throwable $e) {
            $gameView = false;
            $followView = false;
        }

        return $gameView && $followView && ($gameView->getSeasonId() == $followView->getSeasonId());
    }
}
