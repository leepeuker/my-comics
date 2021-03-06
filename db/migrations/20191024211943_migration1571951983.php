<?php

use Phinx\Migration\AbstractMigration;

class Migration1571951983 extends AbstractMigration
{
    public function down() : void
    {
        $this->execute('DROP TABLE `comics`');
        $this->execute('DROP TABLE `images`');
        $this->execute('DROP TABLE `publishers`');
    }

    public function up() : void
    {
        $this->execute(
            'CREATE TABLE `images` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `file_name` VARCHAR(256) NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT NOW(),
                `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE NOW(),
                PRIMARY KEY (`id`),
                UNIQUE (`file_name`)
            ) COLLATE="utf8mb4_general_ci" ENGINE=InnoDB'
        );

        $this->execute(
            'CREATE TABLE `publishers` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `comic_vine_id` INT(10) NOT NULL,
                `name` VARCHAR(256) NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT NOW(),
                `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE NOW(),
                PRIMARY KEY (`id`),
                UNIQUE (`comic_vine_id`),
                UNIQUE (`name`)
            ) COLLATE="utf8mb4_general_ci" ENGINE=InnoDB'
        );

        $this->execute(
            'CREATE TABLE `comics` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `cover_id` INT(10) UNSIGNED NULL DEFAULT NULL,
                `comic_vine_id` INT(10) NULL DEFAULT NULL,
                `name` VARCHAR(256) NOT NULL,
                `year` YEAR(4) NULL DEFAULT NULL,
                `publisher_id` INT(10) UNSIGNED NULL DEFAULT NULL,
                `description` TEXT NOT NULL,
                `added_to_collection` DATETIME NULL DEFAULT NULL,
                `price` INT(8) NULL DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT NOW(),
                `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE NOW(),
                PRIMARY KEY (`id`),
                FOREIGN KEY (`cover_id`) REFERENCES `images`(`id`),
                FOREIGN KEY (`publisher_id`) REFERENCES `publishers`(`id`),
                UNIQUE (`comic_vine_id`)
            ) COLLATE="utf8mb4_general_ci" ENGINE=InnoDB'
        );
    }
}
