<?php

namespace TailgateApi\Validators;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use TailgateApi\Validators\Group\GroupExist;
use TailgateApi\Validators\User\UserExist;

class UpdateGroupCommandValidator extends AbstractRespectValidator
{
    private $userViewRepository;
    private $groupViewRepository;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        UserViewRepositoryInterface $userViewRepository
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->userViewRepository = $userViewRepository;
    }

    protected function addRules($command)
    {
        V::with("TailgateApi\Validators\User\\");
        V::with("TailgateApi\Validators\Group\\");

        $this->rules['name'] = V::notEmpty()->alnum()->noWhitespace()->length(4, 30)->setName('Name');
        $this->rules['groupId'] = V::notEmpty()->stringType()->GroupExist($this->groupViewRepository)->setName('Group');
        $this->rules['ownerId'] = V::notEmpty()->stringType()->UserExist($this->userViewRepository)->setName('Owner');
    }
}
