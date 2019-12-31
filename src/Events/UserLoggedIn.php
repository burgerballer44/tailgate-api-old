<?php

namespace TailgateApi\Events;

use TailgateApi\Events\ApiEvent;

class UserLoggedIn implements ApiEvent
{
    private $userId;
    private $occurredOn;

    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->userId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
