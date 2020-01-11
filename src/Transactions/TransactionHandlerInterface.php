<?php

namespace TailgateApi\Transactions;

interface TransactionHandlerInterface
{
    public function execute(callable $operation);
}
