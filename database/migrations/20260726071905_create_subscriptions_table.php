<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSubscriptionsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('subscriptions', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci']);
        $table->addColumn('follower_id', 'integer')
            ->addColumn('author_id', 'integer')
            ->addIndex(['follower_id', 'author_id'], ['unique' => true])
            ->addForeignKey('follower_id', 'users', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('author_id', 'users', 'id', ['delete' => 'CASCADE'])
            ->create();
    }
    public function down()
    {
        $this->table('subscriptions')->drop()->save();
    }
}
