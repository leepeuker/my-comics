<?php

use Phinx\Migration\AbstractMigration;

class Migration1578599792 extends AbstractMigration
{
    public function down() : void
    {
        $this->execute('ALTER TABLE comics DROP COLUMN rating');
    }

    public function up() : void
    {
        $this->execute('ALTER TABLE comics ADD COLUMN rating TINYINT NULL DEFAULT NULL AFTER price');
    }
}
