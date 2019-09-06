<?php

use Phinx\Migration\AbstractMigration;

class CreateMemberTable extends AbstractMigration
{
    public function up()
    {
        $member = $this->table('member', [
            'id' => false,
            'primary_key' => ['member_id'],
            'collation' => 'utf8mb4_unicode_ci',
            'signed' => false
        ]);
        $member->addColumn('member_id', 'string', ['limit' => 36])
            ->addColumn('group_id', 'string', ['limit' => 36])
            ->addColumn('user_id', 'string', ['limit' => 36])
            ->addColumn('role', 'string', ['limit' => 30])
            ->addColumn('allow_multiple', 'string', ['limit' => 30])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['group_id'])
            ->addIndex(['user_id'])
            ->save();
    }

    public function down()
    {
        $this->table('member')->drop()->save();
    }
}
