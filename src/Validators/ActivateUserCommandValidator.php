<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use TailgateApi\Validators\User\PendingUserExist;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class ActivateUserCommandValidator extends AbstractRespectValidator
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\User\\");

        $this->rules['userId'] = V::notEmpty()->stringType()->PendingUserExist($this->userViewRepository)->setName('User');
    }
}
