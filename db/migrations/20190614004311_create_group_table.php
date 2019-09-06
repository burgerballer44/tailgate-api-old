<?php

use Phinx\Migration\AbstractMigration;

class CreateGroupTable extends AbstractMigration
{
    public function up()
    {
        $group = $this->table('group', [
            'id' => false,
            'primary_key' => ['group_id'],
            'collation' => 'utf8mb4_unicode_ci',
            'signed' => false
        ]);
        $group->addColumn('group_id', 'string', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 30])
            ->addColumn('owner_id', 'string', ['limit' => 36])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['owner_id'])
            ->save();
    }

    public function down()
    {
        $this->table('group')->drop()->save();
    }
}
