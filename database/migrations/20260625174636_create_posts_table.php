<?php

use Phinx\Migration\AbstractMigration;

class CreatePostsTable extends AbstractMigration
{
    public function up()
    {
         $exists = $this->hasTable('posts');
        if (!$exists) {
            $table = $this->table("posts");
            $table->addColumn("title", "string", ["length" => 255])
                ->addColumn("content", "text", ["length" => 255])
                ->addColumn('user_id', 'integer')
                ->create();
            $this->table('posts')
                ->addForeignKey("user_id", "users", "id", ["delete" => "CASCADE"])
                ->update();
        }
    }
    public function down()
    {
        $this->table("posts")->drop()->save();
    }
}
