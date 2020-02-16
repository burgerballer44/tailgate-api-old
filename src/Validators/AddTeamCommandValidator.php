<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\Season\Season;

class AddTeamCommandValidator extends AbstractRespectValidator
{
    protected function addRules($command)
    {
        $this->rules['designation'] = V::notEmpty()->stringType()->length(2, 100)->setName('Designation');
        $this->rules['mascot'] = V::notEmpty()->stringType()->length(2, 50)->setName('Mascot');
        $this->rules['sport'] = V::notEmpty()->in(Season::getValidSports())->setName('Sport');
    }
}
