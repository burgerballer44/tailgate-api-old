<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use TailgateApi\Validators\User\UniqueEmail;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class RegisterUserCommandValidator extends AbstractRespectValidator
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
        parent::__construct();
    }

    protected function messageOverWrites() : array
    {
        return [
            'Password'    => 'Password confirmation does not match.',
            'uniqueEmail' => 'This email is unavailable. Please choose a unique email.',
        ];
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\User\\");

        $this->rules['password'] = V::notEmpty()->stringType()->length(6, 100)->equals($command->getConfirmPassword())->setName('Password');
        $this->rules['email'] = V::notEmpty()->email()->length(4, 100)->UniqueEmail($this->userViewRepository)->setName('Email');
    }
}
