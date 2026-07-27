<?php

use Phinx\Migration\AbstractMigration;

class AddRoleToUsers extends AbstractMigration
{
    public function up()
    {
        $this->table('users')
            ->addColumn('role', 'enum', [
                'values' => ['admin', 'moderator', 'user'],
                'default' => 'user',
            ])
            ->update();
    }
    public function down()
    {
        $this->table('users')
            ->removeColumn('role')
            ->update();
    }
}
