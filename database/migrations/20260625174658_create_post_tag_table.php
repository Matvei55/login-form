<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
class CreatePostTagTable extends AbstractMigration
{
    public function up()
    {
        $exists = $this->hasTable("post_tag");
        if (!$exists) {
            $table = $this->table("post_tag");
            $table->addColumn("post_id", "integer")
                ->addColumn("tag_id", "integer")
                ->create();

            $this->table('post_tag')
                ->addForeignKey("post_id", "posts", "id", ["delete" => "CASCADE"])
                ->addForeignKey("tag_id", "tags", "id", ["delete" => "CASCADE"])
                ->update();
        }
    }

    public function down()
    {
        $this->table("post_tag")->drop()->save();
    }
}
