<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCommentsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('comments', [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci'
        ]);

        $table->addColumn('post_id', 'integer', ['signed' => false])
              ->addColumn('user_id', 'integer', ['signed' => false])
              ->addColumn('parent_id', 'integer', ['null' => true, 'signed' => false])
              ->addColumn('content', 'text')
              ->addColumn('status', 'enum', [
                  'values' => ['pending', 'approved', 'rejected', 'spam'],
                  'default' => 'pending'
              ])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('post_id', 'posts', 'id', [
                  'delete' => 'CASCADE',
                  'update' => 'CASCADE'
              ])
              ->addForeignKey('user_id', 'users', 'id', [
                  'delete' => 'CASCADE',
                  'update' => 'CASCADE'
              ])
              ->addForeignKey('parent_id', 'comments', 'id', [
                  'delete' => 'CASCADE',
                  'update' => 'CASCADE'
              ])
              ->create();
    }
    public function down()
    {
        $this->table('comments')->drop()->save();
    }
}
