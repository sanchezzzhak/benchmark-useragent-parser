<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210903075807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_benchmark_result_source_parser_id');
        $this->addSql('DROP INDEX idx_benchmark_result_user_agent');
        $this->addSql('CREATE TEMPORARY TABLE __temp__benchmark_result AS SELECT id, user_agent, status, last_updated_at, source_parser_id FROM benchmark_result');
        $this->addSql('DROP TABLE benchmark_result');
        $this->addSql('CREATE TABLE benchmark_result (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_agent VARCHAR(500) DEFAULT NULL COLLATE BINARY, status INTEGER DEFAULT NULL, source_parser_id INTEGER NOT NULL, last_updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO benchmark_result (id, user_agent, status, last_updated_at, source_parser_id) SELECT id, user_agent, status, last_updated_at, source_parser_id FROM __temp__benchmark_result');
        $this->addSql('DROP TABLE __temp__benchmark_result');
        $this->addSql('CREATE INDEX idx_benchmark_result_source_parser_id ON benchmark_result (source_parser_id)');
        $this->addSql('CREATE INDEX idx_benchmark_result_user_agent ON benchmark_result (user_agent)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__device_detector_result AS SELECT id, bench_id, time, memory, score, client_name, client_version, parser_id, engine_name, engine_version, os_name, os_version, data_json, device_type, brand_name, model_name, is_bot, bot_name FROM device_detector_result');
        $this->addSql('DROP TABLE device_detector_result');
        $this->addSql('CREATE TABLE device_detector_result (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, bench_id INTEGER NOT NULL, memory INTEGER NOT NULL, score INTEGER NOT NULL, client_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, client_version VARCHAR(255) DEFAULT NULL COLLATE BINARY, parser_id INTEGER NOT NULL, engine_version VARCHAR(255) DEFAULT NULL COLLATE BINARY, os_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, os_version VARCHAR(255) DEFAULT NULL COLLATE BINARY, data_json CLOB DEFAULT NULL COLLATE BINARY, device_type VARCHAR(255) DEFAULT NULL COLLATE BINARY, brand_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, model_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, is_bot BOOLEAN DEFAULT NULL, bot_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, engine_name VARCHAR(255) DEFAULT NULL, time DOUBLE PRECISION NOT NULL)');
        $this->addSql('INSERT INTO device_detector_result (id, bench_id, time, memory, score, client_name, client_version, parser_id, engine_name, engine_version, os_name, os_version, data_json, device_type, brand_name, model_name, is_bot, bot_name) SELECT id, bench_id, time, memory, score, client_name, client_version, parser_id, ут�engine_name, engine_version, os_name, os_version, data_json, device_type, brand_name, model_name, is_bot, bot_name FROM __temp__device_detector_result');
        $this->addSql('DROP TABLE __temp__device_detector_result');
        $this->addSql('CREATE INDEX idx_device_detector_result_bench_id ON device_detector_result (bench_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_benchmark_result_user_agent');
        $this->addSql('DROP INDEX idx_benchmark_result_source_parser_id');
        $this->addSql('CREATE TEMPORARY TABLE __temp__benchmark_result AS SELECT id, user_agent, status, last_updated_at, source_parser_id FROM benchmark_result');
        $this->addSql('DROP TABLE benchmark_result');
        $this->addSql('CREATE TABLE benchmark_result (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_agent VARCHAR(500) DEFAULT NULL, status INTEGER DEFAULT NULL, source_parser_id INTEGER NOT NULL, last_updated_at DATETIME DEFAULT \'NULL --(DC2Type:datetime_immutable)\' --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO benchmark_result (id, user_agent, status, last_updated_at, source_parser_id) SELECT id, user_agent, status, last_updated_at, source_parser_id FROM __temp__benchmark_result');
        $this->addSql('DROP TABLE __temp__benchmark_result');
        $this->addSql('CREATE INDEX idx_benchmark_result_user_agent ON benchmark_result (user_agent)');
        $this->addSql('CREATE INDEX idx_benchmark_result_source_parser_id ON benchmark_result (source_parser_id)');
        $this->addSql('DROP INDEX idx_device_detector_result_bench_id');
        $this->addSql('CREATE TEMPORARY TABLE __temp__device_detector_result AS SELECT id, bench_id, time, memory, score, client_name, client_version, parser_id, engine_name, engine_version, os_name, os_version, data_json, device_type, brand_name, model_name, is_bot, bot_name FROM device_detector_result');
        $this->addSql('DROP TABLE device_detector_result');
        $this->addSql('CREATE TABLE device_detector_result (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, bench_id INTEGER NOT NULL, memory INTEGER NOT NULL, score INTEGER NOT NULL, client_name VARCHAR(255) DEFAULT NULL, client_version VARCHAR(255) DEFAULT NULL, parser_id INTEGER NOT NULL, engine_version VARCHAR(255) DEFAULT NULL, os_name VARCHAR(255) DEFAULT NULL, os_version VARCHAR(255) DEFAULT NULL, data_json CLOB DEFAULT NULL, device_type VARCHAR(255) DEFAULT NULL, brand_name VARCHAR(255) DEFAULT NULL, model_name VARCHAR(255) DEFAULT NULL, is_bot BOOLEAN DEFAULT NULL, bot_name VARCHAR(255) DEFAULT NULL, ут�engine_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, time INTEGER NOT NULL)');
        $this->addSql('INSERT INTO device_detector_result (id, bench_id, time, memory, score, client_name, client_version, parser_id, engine_name, engine_version, os_name, os_version, data_json, device_type, brand_name, model_name, is_bot, bot_name) SELECT id, bench_id, time, memory, score, client_name, client_version, parser_id, engine_name, engine_version, os_name, os_version, data_json, device_type, brand_name, model_name, is_bot, bot_name FROM __temp__device_detector_result');
        $this->addSql('DROP TABLE __temp__device_detector_result');
    }
}
