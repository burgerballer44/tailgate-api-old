<?php

namespace TailgateApi\Validators\User;

use Respect\Validation\Validator as V;
use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class UniqueEmail extends AbstractRule
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
    }

    /**
     * returns false when the user exists by the email
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {
        $user = $this->userViewRepository->byEmail($input);

        return false == $user;
    }
}
