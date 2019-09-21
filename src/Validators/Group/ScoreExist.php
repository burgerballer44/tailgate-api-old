<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Group\ScoreView;

class ScoreExist extends AbstractRule
{
    private $scoreViewRepository;

    public function __construct(ScoreViewRepositoryInterface $scoreViewRepository)
    {
        $this->scoreViewRepository = $scoreViewRepository;
    }

    /**
     * returns false when the Score does not exist
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $scoreView = $this->scoreViewRepository->get(ScoreId::fromString($input));
        } catch (\Throwable $e) {
            $scoreView = false;
        }

        return $scoreView instanceof ScoreView;
    }
}
