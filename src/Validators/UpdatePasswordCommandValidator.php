<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use TailgateApi\Validators\User\UserExist;
use TailgateApi\Validators\User\PasswordMatches;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class UpdatePasswordCommandValidator extends AbstractRespectValidator
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
        $this->rules['password'] = V::notEmpty()->stringType()->length(6, 100)->PasswordMatches($command->getConfirmPassword())->setName('Password');
    }
}
