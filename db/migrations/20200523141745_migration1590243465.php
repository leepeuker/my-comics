<?php

use Phinx\Migration\AbstractMigration;

class Migration1590243465 extends AbstractMigration
{
    public function down() : void
    {
        $this->execute('ALTER TABLE comics DROP INDEX cover_unique');
    }

    public function up() : void
    {
        $this->execute('ALTER TABLE comics ADD CONSTRAINT cover_unique UNIQUE (id, cover_id)');
    }
}
