<?php

use yii\db\Migration;

/**
 * Class m210903_143651_device_detector_result
 */
class m210903_143651_device_detector_result extends Migration
{
    private const TABLE_NAME = 'device_detector_result';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'bench_id' => $this->integer()->notNull(),
            'parser_id' => $this->integer()->notNull(),
            'time' => $this->float()->null(),
            'memory' => $this->integer()->null(),
            'client_name' => $this->string()->null(),
            'client_version' => $this->string()->null(),
            'client_type' => $this->string()->null(),
            'engine_name' => $this->string()->null(),
            'engine_version' => $this->string()->null(),
            'os_name' => $this->string()->null(),
            'os_version' => $this->string()->null(),
            'data_json' => $this->text()->null(),
            'device_type' => $this->string()->null(),
            'brand_name' => $this->string()->null(),
            'model_name' => $this->string()->null(),
            'is_bot' => $this->boolean()->defaultValue(false),
            'bot_name' => $this->string()->null(),
        ]);

        $this->createIndex('idx_device_detector_result-bench_id', self::TABLE_NAME, ['bench_id']);
        $this->createIndex('idx_device_detector_result-parser_id', self::TABLE_NAME, ['parser_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }

}
