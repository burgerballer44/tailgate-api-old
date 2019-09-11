<?php

namespace TailgateApi\Validators\Player;

use Respect\Validation\Validator as V;
use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;

class UniqueUsername extends AbstractRule
{
    private $playerViewRepository;

    public function __construct(PlayerViewRepositoryInterface $playerViewRepository)
    {
        $this->playerViewRepository = $playerViewRepository;
    }

    /**
     * returns false when the player exists by the username
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {
        $player = $this->playerViewRepository->byUsername($input);

        return false == $player;
    }
}
