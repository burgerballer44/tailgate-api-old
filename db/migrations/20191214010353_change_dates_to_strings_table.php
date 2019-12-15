<?php

use Phinx\Migration\AbstractMigration;

class ChangeDatesToStringsTable extends AbstractMigration
{
    public function up()
    {
        $season = $this->table('season');
        $season->changeColumn('season_start', 'string', ['limit' => 255])
            ->changeColumn('season_end', 'string', ['limit' => 255])
            ->save();

        $game = $this->table('game');
        $game->changeColumn('start_date', 'string', ['limit' => 255])
            ->save();
    }

    public function down()
    {
        $season = $this->table('season');
        $season->changeColumn('season_start', 'datetime')
            ->changeColumn('season_end', 'datetime')
            ->save();

        $game = $this->table('game');
        $game->changeColumn('start_date', 'datetime')
            ->save();
    }
}
