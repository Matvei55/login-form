<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateTagsTable extends AbstractMigration
{
    public function up()
    {
        $exists = $this->hasTable("tags");
        if (!$exists) {
            $table = $this->table("tags");
                $table->addColumn("name", "string", ["length" => 255])
                    ->create();
        }
    }
    public function down()
    {
        $this->table("tags")->drop()->save();
    }
}
