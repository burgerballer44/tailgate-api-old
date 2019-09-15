<?php

namespace TailgateApi\Validators\User;

use Respect\Validation\Validator as V;
use Respect\Validation\Rules\AbstractRule;

class PasswordMatches extends AbstractRule
{
    private $stringToCompare;

    public function __construct($stringToCompare)
    {
        $this->stringToCompare = $stringToCompare;
    }

    /**
     * returns false when the user exists by the email
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {
        return $input === $this->stringToCompare;
    }
}
