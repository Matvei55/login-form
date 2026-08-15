<?php


use Phinx\Migration\AbstractMigration;

class AddStatusToPosts extends AbstractMigration
{

    public function up()
    {
        $this->table('posts')
            ->addColumn('status' , 'enum', [
                'values' => ['pending', 'approved', 'draft'],
                'default' => 'pending'
                ])
            ->update();
    }
    public function down()
    {
        $this->table('posts')
            ->removeColumn('status')
            ->update();
    }
}
