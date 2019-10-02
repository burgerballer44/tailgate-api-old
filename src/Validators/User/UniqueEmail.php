<?php

namespace TailgateApi\Validators\User;

use Respect\Validation\Validator as V;
use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class UniqueEmail extends AbstractRule
{
    private $userViewRepository;
    private $userId;

    public function __construct(UserViewRepositoryInterface $userViewRepository, $userId = false)
    {
        $this->userViewRepository = $userViewRepository;
        $this->userId = $userId;
    }

    /**
     * returns false when the user exists by email 
     * if userId property is set then the userView userId is checked
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {
        $userView = $this->userViewRepository->byEmail($input);

        // return true if we want to allow the userId to bypass check if it is their own email
        if ($this->userId && $userView) {
            return $this->userId === $userView->getUserId();
        }

        return false == $userView;
    }
}
