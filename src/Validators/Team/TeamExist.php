<?php

namespace TailgateApi\Validators\Team;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamView;

class TeamExist extends AbstractRule
{
    private $teamViewRepository;

    public function __construct(TeamViewRepositoryInterface $teamViewRepository)
    {
        $this->teamViewRepository = $teamViewRepository;
    }

    /**
     * returns false when the team does not exist
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $teamView = $this->teamViewRepository->get(TeamId::fromString($input));
        } catch (\Throwable $e) {
            $teamView = false;
        }

        return $teamView instanceof TeamView;
    }
}
