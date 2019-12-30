<?php

use Phinx\Migration\AbstractMigration;

class Migration1576319953 extends AbstractMigration
{
    public function down() : void
    {
        $this->execute('ALTER TABLE comics MODIFY added_to_collection DATETIME');
    }

    public function up() : void
    {
        $this->execute('ALTER TABLE comics MODIFY added_to_collection DATE');
    }
}
