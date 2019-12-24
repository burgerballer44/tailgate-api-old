<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Application\Command\Season\UpdateSeasonCommand;
use Tailgate\Domain\Service\Season\UpdateSeasonHandler;

// update a season
class UpdateSeasonAction extends AbstractAction
{   
    private $updateSeasonHandler;

    public function __construct(UpdateSeasonHandler $updateSeasonHandler)
    {
        $this->updateSeasonHandler = $updateSeasonHandler;
    }

    public function action() : ResponseInterface
    {
        extract($this->args);
        $parsedBody = $this->request->getParsedBody();

        $command = new UpdateSeasonCommand(
            $seasonId,
            $parsedBody['name'] ?? '',
            $parsedBody['sport'] ?? '',
            $parsedBody['seasonType'] ?? '',
            $parsedBody['seasonStart'] ?? '',
            $parsedBody['seasonEnd'] ?? ''
        );

        $this->updateSeasonHandler->handle($command);

        return $this->respond();
    }
}