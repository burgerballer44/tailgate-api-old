<?php

use Phinx\Migration\AbstractMigration;

class AddStartTimeColumnToGameTable extends AbstractMigration
{
    public function up()
    {
        $game = $this->table('game');
        $game->addColumn('start_time', 'string', ['limit' => 255, 'after' => 'start_date'])
            ->save();
    }

    public function down()
    {
        $game = $this->table('game');
        $game->removeColumn('start_time')->save();
    }
}
