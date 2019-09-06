<?php

use Phinx\Migration\AbstractMigration;

class CreateFollowTable extends AbstractMigration
{
    public function up()
    {
        $follow = $this->table('follow', [
            'id' => false,
            'primary_key' => ['follow_id'],
            'collation' => 'utf8mb4_unicode_ci',
            'signed' => false
        ]);
        $follow->addColumn('follow_id', 'string', ['limit' => 36])
            ->addColumn('group_id', 'string', ['limit' => 36])
            ->addColumn('team_id', 'string', ['limit' => 36])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['group_id'])
            ->addIndex(['team_id'])
            ->save();
    }

    public function down()
    {
        $this->table('follow')->drop()->save();
    }
}
