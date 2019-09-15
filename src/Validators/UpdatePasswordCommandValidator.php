<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use TailgateApi\Validators\User\PasswordMatches;

class UpdatePasswordCommandValidator extends AbstractRespectValidator
{
    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\User\\");
        
        $this->rules['password'] = V::notEmpty()->stringType()->length(6, 100)->PasswordMatches($command->getConfirmPassword())->setName('Password');
    }
}
