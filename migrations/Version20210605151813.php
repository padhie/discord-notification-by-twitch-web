<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210605151813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add Settings';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE settings (
                id INT AUTO_INCREMENT NOT NULL,
                `key` VARCHAR(255) NOT NULL,
                `value` VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB
        ');

        $this->addSql("INSERT INTO settings SET `key`='notificationActive', `value`='1'");
        $this->addSql("INSERT INTO settings SET `key`='notificationInactiveUntil', `value`='2021-06-05'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE settings');
    }
}
