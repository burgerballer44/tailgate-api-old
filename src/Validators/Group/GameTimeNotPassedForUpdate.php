<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Group\ScoreView;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;

class GameTimeNotPassedForUpdate extends AbstractRule
{
    private $scoreViewRepository;
    private $gameViewRepository;

    public function __construct(
        ScoreViewRepositoryInterface $scoreViewRepository,
        GameViewRepositoryInterface $gameViewRepository
    ) {
        $this->scoreViewRepository = $scoreViewRepository;
        $this->gameViewRepository = $gameViewRepository;
    }

    /**
     * returns false when the game time has already passed
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $scoreView = $this->scoreViewRepository->get(ScoreId::fromString($input));
            $gameView = $this->gameViewRepository->get(GameId::fromString($scoreView->getGameId()));
        } catch (\Throwable $e) {
            $gameView = false;
        }

        if ($gameView) {

            // get the date and time of the game
            $gameDateTime = \DateTimeImmutable::createFromFormat('M j, Y (D) g:i A', $gameView->getStartDate() . " " . $gameView->getStartTime());
            if ($gameDateTime instanceof \DateTimeImmutable) {
                $today = (new \DateTime('now'))->format('Y-m-d H:i:s');
                $gameStart = $gameDateTime->format('Y-m-d H:i:s');
                return $today < $gameStart;
            } 

            // if creating the date time object fails then the game time is probably 'TBA' or something like that so just use the game date
            $gameDateTime = $gameDateTime = \DateTimeImmutable::createFromFormat('M j, Y (D)', $gameView->getStartDate());

            if ($gameDateTime instanceof \DateTimeImmutable) {
                $today = (new \DateTime('now'))->format('Y-m-d');
                $gameStart = $gameDateTime->format('Y-m-d');
                return $today <= $gameStart;
            }
        }

        return false;
    }
}
