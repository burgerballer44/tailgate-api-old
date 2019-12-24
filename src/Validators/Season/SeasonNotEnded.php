<?php

namespace TailgateApi\Validators\Season;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonView;

class SeasonNotEnded extends AbstractRule
{
    private $seasonViewRepository;

    public function __construct(SeasonViewRepositoryInterface $seasonViewRepository)
    {
        $this->seasonViewRepository = $seasonViewRepository;
    }

    /**
     * returns false when the Season has ended
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $seasonView = $this->seasonViewRepository->get(SeasonId::fromString($input));
        } catch (\Throwable $e) {
            $seasonView = false;
        }

        if ($seasonView) {
            $today = (new \DateTime('today'))->format('Y-m-d');
            $seasonEnd = \DateTimeImmutable::createFromFormat("Y-m-d", $seasonView->getSeasonEnd())->format("Y-m-d");
            return $seasonEnd < $today;
        }

        return false;
    }
}
