<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\User\ResetPasswordCommand;
use Tailgate\Application\Query\User\UserResetPasswordTokenQuery;
use Tailgate\Domain\Service\User\ResetPasswordHandler;
use Tailgate\Domain\Service\User\UserResetPasswordTokenQueryHandler;

// passwords can be updated with a token
class ResetPasswordAction extends AbstractAction
{   
    private $resetPasswordHandler;
    private $userResetPasswordTokenQueryHandler;

    public function __construct(
        ResetPasswordHandler $resetPasswordHandler,
        UserResetPasswordTokenQueryHandler $userResetPasswordTokenQueryHandler
    ) {
        $this->resetPasswordHandler = $resetPasswordHandler;
        $this->userResetPasswordTokenQueryHandler = $userResetPasswordTokenQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();
        $passwordResetToken = $parsedBody['passwordResetToken'];

        // get user by token
        $user = $this->userResetPasswordTokenQueryHandler->handle(new UserResetPasswordTokenQuery($passwordResetToken));
        $userId = $user['userId'];

        $command = new ResetPasswordCommand(
            $userId,
            $parsedBody['password'] ?? '',
            $parsedBody['confirmPassword'] ?? ''
        );
        $this->resetPasswordHandler->handle($command);

        return $this->respond();
    }
}