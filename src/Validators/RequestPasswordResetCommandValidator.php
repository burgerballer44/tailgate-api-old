<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use TailgateApi\Validators\User\UserExist;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class RequestPasswordResetCommandValidator extends AbstractRespectValidator
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\User\\");
        
        $this->rules['userId'] = V::notEmpty()->stringType()->UserExist($this->userViewRepository)->setName('User');
    }
}
