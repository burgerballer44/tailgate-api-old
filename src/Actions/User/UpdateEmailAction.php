<?php

namespace TailgateApi\Actions\User;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\User\UpdateEmailCommand;
use Tailgate\Domain\Service\User\UpdateEmailHandler;

// update email of authenticated user
class UpdateEmailAction extends AbstractAction
{   
    private $updateEmailHandler;

    public function __construct(UpdateEmailHandler $updateEmailHandler)
    {
        $this->updateEmailHandler = $updateEmailHandler;
    }

    public function action() : ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');

        $parsedBody = $this->request->getParsedBody();

        $command = new UpdateEmailCommand(
            $userId,
            $parsedBody['email'] ?? ''
        );

        $this->updateEmailHandler->handle($command);

        return $this->respond();
    }
}