<?php

use Phinx\Migration\AbstractMigration;

class CreatePlayersTable extends AbstractMigration
{
    public function up()
    {
        $player = $this->table('player', [
            'id' => false,
            'primary_key' => ['player_id'],
            'collation' => 'utf8mb4_unicode_ci',
            'signed' => false
        ]);
        $player->addColumn('player_id', 'string', ['limit' => 36])
            ->addColumn('member_id', 'string', ['limit' => 36])
            ->addColumn('group_id', 'string', ['limit' => 36])
            ->addColumn('username', 'string', ['limit' => 20])
            ->addColumn('created_at', 'datetime')
            ->save();
      }

    public function down()
    {
        $this->table('player')->drop()->save();
    }
}
