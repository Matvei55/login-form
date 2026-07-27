<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoriesTable extends AbstractMigration
{
    public function up()
    {
        $table=$this->table('categories');
        $table->addColumn('title', 'string', ['limit' => 100])
            ->addColumn('slug', 'string', ['limit' => 100])
            ->addIndex(['slug'], ['unique' => true])
            ->create();
    }

    public function down()
    {
        $this->table('categories')->drop()->save();
    }

}
