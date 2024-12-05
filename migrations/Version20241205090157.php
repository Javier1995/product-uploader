<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205090157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (intProductDataId INT UNSIGNED AUTO_INCREMENT NOT NULL, strt_product_name VARCHAR(50) NOT NULL, str_product_desc VARCHAR(255) NOT NULL, str_product_code VARCHAR(10) NOT NULL, dtm_added DATETIME DEFAULT NULL, dtm_discontinued DATETIME DEFAULT NULL, stm_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX UNIQ_D34A04AD32E6B28B (str_product_code), PRIMARY KEY(intProductDataId)) DEFAULT CHARACTER SET latin1 ENGINE = InnoDB COMMENT = \'Stores product data\' ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE product');
    }
}
