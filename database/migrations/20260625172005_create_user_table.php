<?php


use Phinx\Migration\AbstractMigration;

 class CreateUserTable extends AbstractMigration
{
    public function up()
    {

        $exists = $this->hasTable('users');
        if (!$exists) {
            $table = $this->table("users");
            $table->addColumn("name", "string", ["length" => 255])
                ->addColumn('password', 'string', ['limit' => 255])
                ->create();
        }
    }

    public function down()
    {
        $this->table("users")->drop()->save();
    }
}
