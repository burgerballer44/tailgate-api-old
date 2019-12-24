<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Season\CreateSeasonCommand;
use Tailgate\Domain\Service\Season\CreateSeasonHandler;

// create a season
class CreateSeasonAction extends AbstractAction
{   
    private $createSeasonHandler;

    public function __construct(CreateSeasonHandler $createSeasonHandler)
    {
        $this->createSeasonHandler = $createSeasonHandler;
    }

    public function action() : ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();

        $command = new CreateSeasonCommand(
            $parsedBody['name'] ?? '',
            $parsedBody['sport'] ?? '',
            $parsedBody['seasonType'] ?? '',
            $parsedBody['seasonStart'] ?? '',
            $parsedBody['seasonEnd'] ?? ''
        );

        $this->createSeasonHandler->handle($command);
        return $this->respond();
    }
}