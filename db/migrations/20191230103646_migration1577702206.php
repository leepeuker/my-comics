<?php

use Phinx\Migration\AbstractMigration;

class Migration1577702206 extends AbstractMigration
{
    public function down() : void
    {
        $this->execute('UPDATE `images` SET `file_name` = CONCAT("images/", `file_name`)');
    }

    public function up() : void
    {
        $this->execute('UPDATE `images` SET `file_name` = REPLACE(`file_name`, "images/", "")');
    }
}
