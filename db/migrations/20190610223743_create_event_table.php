<?php

use Phinx\Migration\AbstractMigration;

class CreateEventTable extends AbstractMigration
{
    public function up()
    {
        $user = $this->table('event', ['collation' => 'utf8mb4_unicode_ci', 'signed' => false]);
        $user->addColumn('aggregate_id', 'string', ['limit' => 36])
            ->addColumn('type', 'string', ['limit' => 255])
            ->addColumn('created_at', 'datetime')
            ->addColumn('data', 'text')
            ->save();
    }

    public function down()
    {
        $this->table('event')->drop()->save();
    }
}