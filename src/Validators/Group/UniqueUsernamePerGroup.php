<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Validator as V;
use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupId;

class UniqueUsernamePerGroup extends AbstractRule
{
    private $playerViewRepository;
    private $groupId;

    public function __construct(PlayerViewRepositoryInterface $playerViewRepository, $groupId)
    {
        $this->playerViewRepository = $playerViewRepository;
        $this->groupId = $groupId;
    }

    /**
     * returns false when the player exists by username in the group
     * @param  [type] $input [description]
     * @param  [type] $groupId [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {
        $players = $this->playerViewRepository->getAllByGroup(GroupId::fromString($this->groupId));
        
        $playerExistWithUsername = empty(array_filter($players, function($player) use ($input) {
            return $player->getUsername() == $input;
        }));

        return $playerExistWithUsername;
    }
}
