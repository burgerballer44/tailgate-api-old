<?php

use Phinx\Migration\AbstractMigration;

class CreateEventLogTable extends AbstractMigration
{
    public function up()
    {
        $user = $this->table('event_log', ['collation' => 'utf8mb4_unicode_ci', 'signed' => false]);
        $user->addColumn('aggregate_id', 'string', ['limit' => 36])
            ->addColumn('type', 'string', ['limit' => 255])
            ->addColumn('created_at', 'datetime')
            ->addColumn('data', 'text')
            ->save();
    }

    public function down()
    {
        $this->table('event_log')->drop()->save();
    }
}