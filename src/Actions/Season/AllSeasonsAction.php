<?php

namespace TailgateApi\Actions\Season;

use Psr\Http\Message\ResponseInterface;
use TailgateApi\Actions\AbstractAction;
use Tailgate\Domain\Service\Season\AllSeasonsQueryHandler;

// view all seasons
class AllSeasonsAction extends AbstractAction
{   
    private $allSeasonsQueryHandler;

    public function __construct(AllSeasonsQueryHandler $allSeasonsQueryHandler)
    {
        $this->allSeasonsQueryHandler = $allSeasonsQueryHandler;
    }

    public function action() : ResponseInterface
    {
        $seasons = $this->allSeasonsQueryHandler->handle();
        return $this->respondWithData($seasons);
    }
}