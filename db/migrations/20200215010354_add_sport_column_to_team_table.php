<?php

use Phinx\Migration\AbstractMigration;

class AddSportColumnToTeamTable extends AbstractMigration
{
    public function up()
    {
        $this->table('team')
            ->addColumn('sport', 'string', ['limit' => 50, 'after' => 'mascot'])
            ->addIndex(['sport'])
            ->save();
    }

    public function down()
    {
        $this->table('follow')->removeColumn('season_id')->save();
    }
}
