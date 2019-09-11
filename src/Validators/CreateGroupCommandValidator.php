<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;

class CreateGroupCommandValidator extends AbstractRespectValidator
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
        $this->rules['name'] = V::notEmpty()->alnum()->noWhitespace()->length(4, 30)->setName('Name');
        $this->rules['owner_id'] = V::notEmpty()->stringType()->setName('Owner');
    }
}
