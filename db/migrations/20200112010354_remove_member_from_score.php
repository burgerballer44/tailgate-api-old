<?php

use Phinx\Migration\AbstractMigration;

class RemoveMemberFromScore extends AbstractMigration
{
    public function up()
    {
        $score = $this->table('score');
        $score->removeColumn('member_id')->save();
    }

    public function down()
    {
        $score = $this->table('score');
        $score->addColumn('member_id', 'string', ['limit' => 36, 'after' => 'player_id']);
        $score->save();
    }
}
