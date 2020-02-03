<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Season\Season;

class CreateSeasonCommandValidator extends AbstractRespectValidator
{
    protected function addRules($command)
    {
        $this->rules['name'] = V::notEmpty()->stringType()->length(4, 100)->setName('Name');
        $this->rules['sport'] = V::notEmpty()->in(Season::getValidSports())->setName('Sport');
        $this->rules['seasonType'] = V::notEmpty()->in(Season::getValidSeasonTypes())->setName('Season Type');
        $this->rules['seasonStart'] = V::notEmpty()->date('Y-m-d')->setName('Start of Season');
        $this->rules['seasonEnd'] = V::notEmpty()->date('Y-m-d')->setName('End of Season');
    }
}
