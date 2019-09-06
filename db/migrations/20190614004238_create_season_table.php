<?php

use Phinx\Migration\AbstractMigration;

class CreateSeasonTable extends AbstractMigration
{
    public function up()
    {
        $season = $this->table('season', [
            'id' => false,
            'primary_key' => ['season_id'],
            'collation' => 'utf8mb4_unicode_ci',
            'signed' => false
        ]);
        $season->addColumn('season_id', 'string', ['limit' => 36])
            ->addColumn('sport', 'string', ['limit' => 50])
            ->addColumn('type', 'string', ['limit' => 50])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('season_start', 'datetime')
            ->addColumn('season_end', 'datetime')
            ->addColumn('created_at', 'datetime')
            ->save();
    }

    public function down()
    {
        $this->table('season')->drop()->save();
    }
}
