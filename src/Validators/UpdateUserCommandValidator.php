<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use TailgateApi\Validators\User\UserExist;
use TailgateApi\Validators\User\UniqueEmail;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Model\User\User;

class UpdateUserCommandValidator extends AbstractRespectValidator
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
        $this->rules['email'] = V::notEmpty()->email()->length(4, 100)->UniqueEmail($this->userViewRepository, $command->getUserId())->setName('Email');
        $this->rules['status'] = V::notEmpty()->in(User::getValidStatuses())->setName('Status');
        $this->rules['role'] = V::notEmpty()->in(User::getValidRoles())->setName('Role');
    }
}
