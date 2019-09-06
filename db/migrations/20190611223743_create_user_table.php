<?php

use Phinx\Migration\AbstractMigration;

class CreateUserTable extends AbstractMigration
{
    public function up()
    {
        $user = $this->table('user', [
            'id' => false,
            'primary_key' => ['user_id'],
            'collation' => 'utf8mb4_unicode_ci',
            'signed' => false
        ]);
        $user->addColumn('user_id', 'string', ['limit' => 36])
            // ->addColumn('username', 'string', ['limit' => 20])
            ->addColumn('password_hash', 'string', ['limit' => 255])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('status', 'string', ['limit' => 100])
            ->addColumn('role', 'string', ['limit' => 30])
            ->addColumn('unique_key', 'string', ['limit' => 36])
            ->addColumn('created_at', 'datetime')
            // ->addIndex(['username'], ['unique' => true])
            ->addIndex(['email'], ['unique' => true])
            ->save();
    }

    public function down()
    {
        $this->table('user')->drop()->save();
    }
}