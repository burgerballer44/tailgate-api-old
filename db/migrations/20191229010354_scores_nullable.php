<?php

use Phinx\Migration\AbstractMigration;

class ScoresNullable extends AbstractMigration
{
    public function up()
    {
        $game = $this->table('game');
        $game->changeColumn('home_team_score', 'integer', ['null' => true])->save();
        $game->changeColumn('away_team_score', 'integer', ['null' => true])->save();
    }

    public function down()
    {
        $game = $this->table('game');
        $game->changeColumn('home_team_score', 'integer', ['null' => false])->save();
        $game->changeColumn('away_team_score', 'integer', ['null' => false])->save();
    }
}