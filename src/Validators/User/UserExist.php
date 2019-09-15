<?php

namespace TailgateApi\Validators\User;

use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserView;

class UserExist extends AbstractRule
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
    }

    /**
     * returns false when the user does not exist
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {   
        try {
            $userView = $this->userViewRepository->get(UserId::fromString($input));
        } catch (\Throwable $e) {
            $userView = false;
        }

        return $userView instanceof UserView;
    }
}
