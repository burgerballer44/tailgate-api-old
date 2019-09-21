<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\PlayerView;

class PlayerExist extends AbstractRule
{
    private $playerViewRepository;

    public function __construct(PlayerViewRepositoryInterface $playerViewRepository)
    {
        $this->playerViewRepository = $playerViewRepository;
    }

    /**
     * returns false when the Player does not exist
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $playerView = $this->playerViewRepository->get(PlayerId::fromString($input));
        } catch (\Throwable $e) {
            $playerView = false;
        }

        return $playerView instanceof PlayerView;
    }
}
