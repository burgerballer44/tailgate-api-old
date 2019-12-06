<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Season\Season;
use TailgateApi\Validators\Season\SeasonExist;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;

class UpdateSeasonCommandValidator extends AbstractRespectValidator
{
    private $seasonViewRepository;

    public function __construct(SeasonViewRepositoryInterface $seasonViewRepository)
    {
        $this->seasonViewRepository = $seasonViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\Season\\");

        $this->rules['seasonId'] = V::notEmpty()->SeasonExist($this->seasonViewRepository)->setName('Season');
        $this->rules['name'] = V::notEmpty()->alnum()->length(4, 100)->setName('Name');
        $this->rules['sport'] = V::notEmpty()->in(Season::getValidSports())->setName('Sport');
        $this->rules['seasonType'] = V::notEmpty()->in(Season::getValidSeasonTypes())->setName('Season Type');
        $this->rules['seasonStart'] = V::notEmpty()->date('Y-m-d')->setName('Start of Season');
        $this->rules['seasonEnd'] = V::notEmpty()->date('Y-m-d')->setName('End of Season');
    }
}
