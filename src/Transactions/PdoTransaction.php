<?php

namespace TailgateApi\Transactions;

use PDO;
use Exception;
use TailgateApi\Transactions\TransactionHandlerInterface;

class PdoTransaction implements TransactionHandlerInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function execute(callable $operation)
    {
        $this->pdo->beginTransaction();

        try {

            $outcome = $operation();

            $this->pdo->commit();

            return $outcome;

        } catch (Exception $e) {

            $this->pdo->rollBack();
            throw $e;
            
        }
    }
}