<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;

class AddTeamCommandValidator extends AbstractRespectValidator
{
    protected function addRules($command)
    {
        $this->rules['designation'] = V::notEmpty()->stringType()->length(2, 100)->setName('Designation');
        $this->rules['mascot'] = V::notEmpty()->stringType()->length(2, 50)->setName('Mascot');
    }
}
