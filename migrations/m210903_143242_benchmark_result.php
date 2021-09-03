<?php

use yii\db\Migration;

/**
 * Class m210903_143242_benchmark_result
 */
class m210903_143242_benchmark_result extends Migration
{

    private const TABLE_NAME = 'benchmark_result';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'user_agent' => $this->string(500)->null(),
            'source_id' => $this->integer()->null(),
        ]);

        $this->createIndex('idx_benchmark_result-user_agent', self::TABLE_NAME, ['user_agent']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }

}
