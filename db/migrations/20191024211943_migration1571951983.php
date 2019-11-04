<?php

use Phinx\Migration\AbstractMigration;

class Migration1571951983 extends AbstractMigration
{
    public function down()
    {
        $this->execute('DROP TABLE `issues`');
        $this->execute('DROP TABLE `images`');
        $this->execute('DROP TABLE `publishers`');
        $this->execute('DROP TABLE `volumes`');
    }

    public function up()
    {
        $this->execute(
            'CREATE TABLE `images` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `file_name` VARCHAR(256) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`file_name`)
            ) COLLATE="utf8mb4_general_ci" ENGINE=InnoDB'
        );

        $this->execute(
            'CREATE TABLE `publishers` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `comic_vine_id` INT(10) NOT NULL,
                `name` VARCHAR(256) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`comic_vine_id`),
                UNIQUE (`name`)
            ) COLLATE="utf8mb4_general_ci" ENGINE=InnoDB'
        );

        $this->execute(
            'CREATE TABLE `volumes` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `comic_vine_id` INT(10) NOT NULL,
                `name` VARCHAR(256) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`comic_vine_id`)
            ) COLLATE="utf8mb4_general_ci" ENGINE=InnoDB'
        );

        $this->execute(
            'CREATE TABLE `issues` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `cover_id` INT(10) UNSIGNED NULL DEFAULT NULL,
                `comic_vine_id` INT(10) NOT NULL,
                `name` VARCHAR(256) NOT NULL,
                `year` YEAR(4) NULL DEFAULT NULL,
                `volume_id` INT(10) UNSIGNED NULL DEFAULT NULL,
                `publisher_id` INT(10) UNSIGNED NULL DEFAULT NULL,
                `description` TEXT NOT NULL,
                `price` INT(8) NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`cover_id`) REFERENCES `images`(`id`),
                FOREIGN KEY (`volume_id`) REFERENCES `volumes`(`id`),
                FOREIGN KEY (`publisher_id`) REFERENCES `publishers`(`id`),
                UNIQUE (`comic_vine_id`)
            ) COLLATE="utf8mb4_general_ci" ENGINE=InnoDB'
        );
    }
}
