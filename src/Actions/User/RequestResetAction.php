<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\User\RequestPasswordResetCommand;
use Tailgate\Application\Query\User\UserEmailQuery;
use Tailgate\Domain\Service\User\RequestPasswordResetHandler;
use Tailgate\Domain\Service\User\UserEmailQueryHandler;

// provides a password reset request key
class RequestResetAction extends AbstractAction
{   
    private $requestPasswordResetHandler;
    private $userEmailQueryHandler;

    public function __construct(
        RequestPasswordResetHandler $requestPasswordResetHandler,
        UserEmailQueryHandler $userEmailQueryHandler
    ) {
        $this->requestPasswordResetHandler = $requestPasswordResetHandler;
        $this->userEmailQueryHandler = $userEmailQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();
        $email = $parsedBody['email'];

        // get user by email
        $user = $this->userEmailQueryHandler->handle(new UserEmailQuery($email));
        $userId = $user['userId'];

        $command = new RequestPasswordResetCommand($userId);
        $user = $this->requestPasswordResetHandler->handle($command);
        return $this->respondWithData($user);
    }
}