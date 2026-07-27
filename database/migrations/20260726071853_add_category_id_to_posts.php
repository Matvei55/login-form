<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddCategoryIdToPosts extends AbstractMigration
{
    public function up()
    {
        $this->table('posts')
            ->addColumn('category_id', 'integer', [
                'null' => true,
                'signed' => false,
            ])
            ->addForeignKey('category_id', 'categories', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->update();
    }
    public function down()
    {
        $this->table('posts')
            ->dropForeignKey('category_id')
            ->removeColumn('category_id')
            ->update();
    }
}
