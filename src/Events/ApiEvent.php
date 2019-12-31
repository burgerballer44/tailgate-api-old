<?php

namespace TailgateApi\Events;

interface ApiEvent
{
    public function getAggregateId();
}