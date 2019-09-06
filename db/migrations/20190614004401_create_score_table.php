<?php

use Phinx\Migration\AbstractMigration;

class CreateScoreTable extends AbstractMigration
{
    public function up()
    {
        $score = $this->table('score', [
            'id' => false,
            'primary_key' => ['score_id'],
            'collation' => 'utf8mb4_unicode_ci',
            'signed' => false
        ]);
        $score->addColumn('score_id', 'string', ['limit' => 36])
            ->addColumn('group_id', 'string', ['limit' => 36])
            ->addColumn('player_id', 'string', ['limit' => 36])
            ->addColumn('game_id', 'string', ['limit' => 36])
            ->addColumn('home_team_prediction', 'integer')
            ->addColumn('away_team_prediction', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addIndex(['group_id'])
            ->addIndex(['player_id'])
            ->addIndex(['game_id'])
            ->save();
    }

    public function down()
    {
        $this->table('score')->drop()->save();
    }
}
