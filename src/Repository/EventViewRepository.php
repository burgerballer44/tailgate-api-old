<?php

namespace TailgateApi\Repository;

use PDO;

class EventViewRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function allById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM event_log WHERE aggregate_id = :aggregate_id ORDER BY CREATED_AT');
        $stmt->execute([':aggregate_id' => (string) $id]);

        $events = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $events[] = $row;
        }

        return $events;
    }
}
