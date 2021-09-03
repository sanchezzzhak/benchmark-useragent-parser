<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210902130250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE benchmark_result (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_agent VARCHAR(500) DEFAULT NULL, status INTEGER DEFAULT NULL, last_updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , source_parser_id INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX idx_benchmark_result_user_agent ON benchmark_result (user_agent)');
        $this->addSql('CREATE INDEX idx_benchmark_result_source_parser_id ON benchmark_result (source_parser_id)');
        $this->addSql('CREATE TABLE device_detector_result (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, bench_id INTEGER NOT NULL, time INTEGER NOT NULL, memory INTEGER NOT NULL, score INTEGER NOT NULL, client_name VARCHAR(255) DEFAULT NULL, client_version VARCHAR(255) DEFAULT NULL, parser_id INTEGER NOT NULL, engine_name VARCHAR(255) DEFAULT NULL, engine_version VARCHAR(255) DEFAULT NULL, os_name VARCHAR(255) DEFAULT NULL, os_version VARCHAR(255) DEFAULT NULL, data_json CLOB DEFAULT NULL, device_type VARCHAR(255) DEFAULT NULL, brand_name VARCHAR(255) DEFAULT NULL, model_name VARCHAR(255) DEFAULT NULL, is_bot BOOLEAN DEFAULT NULL, bot_name VARCHAR(255) DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE benchmark_result');
        $this->addSql('DROP TABLE device_detector_result');
    }
}
