<?php

use Phinx\Migration\AbstractMigration;

class AddSeasonColumnToFollowTable extends AbstractMigration
{
    public function up()
    {
        $this->table('follow')->addColumn('season_id', 'string', ['limit' => 36, 'after' => 'team_id'])->save();
    }

    public function down()
    {
        $this->table('follow')->removeColumn('season_id')->save();
    }
}
