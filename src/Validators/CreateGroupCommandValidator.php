<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use TailgateApi\Validators\Group\GroupLimit;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use TailgateApi\Validators\User\UserExist;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class CreateGroupCommandValidator extends AbstractRespectValidator
{
    private $userViewRepository;
    private $memberViewRepository;

    public function __construct(
        UserViewRepositoryInterface $userViewRepository,
        MemberViewRepositoryInterface $memberViewRepository
    ) {
        $this->userViewRepository = $userViewRepository;
        $this->memberViewRepository = $memberViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\User\\");
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['name'] = V::notEmpty()->alnum()->noWhitespace()->length(4, 30)->setName('Name');
        $this->rules['ownerId'] = V::notEmpty()->stringType()->UserExist($this->userViewRepository)->GroupLimit($this->memberViewRepository)->setName('Owner');
    }
}
