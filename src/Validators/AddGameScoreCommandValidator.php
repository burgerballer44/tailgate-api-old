<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;

class UpdateGameScoreCommandValidator extends AbstractRespectValidator
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function messageOverWrites() : array
    {
        return [
        ];
    }

    protected function addRules($command)
    {
    }
}
