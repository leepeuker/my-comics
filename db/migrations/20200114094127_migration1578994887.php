<?php

use Phinx\Migration\AbstractMigration;

class Migration1578994887 extends AbstractMigration
{
    public function down() : void
    {
        $this->execute('ALTER TABLE publishers MODIFY comic_vine_id INT NOT NULL ');
    }

    public function up() : void
    {
        $this->execute('ALTER TABLE publishers MODIFY comic_vine_id INT NULL DEFAULT NULL');
    }
}
