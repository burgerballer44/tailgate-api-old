<?php

namespace TailgateApi\Validators\Season;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\GameView;

class GameExist extends AbstractRule
{
    private $gameViewRepository;

    public function __construct(GameViewRepositoryInterface $gameViewRepository)
    {
        $this->gameViewRepository = $gameViewRepository;
    }

    /**
     * returns false when the Game does not exist
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $gameView = $this->gameViewRepository->get(GameId::fromString($input));
        } catch (\Throwable $e) {
            $gameView = false;
        }

        return $gameView instanceof GameView;
    }
}
