<?php

use Phinx\Migration\AbstractMigration;

class CreateGameTable extends AbstractMigration
{
    public function up()
    {
        $game = $this->table('game', [
            'id' => false,
            'primary_key' => ['game_id'],
            'collation' => 'utf8mb4_unicode_ci',
            'signed' => false
        ]);
        $game->addColumn('game_id', 'string', ['limit' => 36])
            ->addColumn('season_id', 'string', ['limit' => 36])
            ->addColumn('home_team_id', 'string', ['limit' => 36])
            ->addColumn('away_team_id', 'string', ['limit' => 36])
            ->addColumn('home_team_score', 'integer')
            ->addColumn('away_team_score', 'integer')
            ->addColumn('start_date', 'datetime')
            ->addColumn('created_at', 'datetime')
            ->addIndex(['season_id'])
            ->addIndex(['home_team_id'])
            ->addIndex(['away_team_id'])
            ->save();
    }

    public function down()
    {
        $this->table('game')->drop()->save();
    }
}
