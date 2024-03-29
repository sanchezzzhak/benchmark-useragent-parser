<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "benchmark_result".
 *
 * @property int $id
 * @property string|null $user_agent
 * @property int|null $source_id
 * @property DeviceDetectorResult[] $parseResults
 */
class BenchmarkResult extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'benchmark_result';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['source_id'], 'integer'],
            [['user_agent'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_agent' => 'User Agent',
            'source_id' => 'Source ID',
        ];
    }

    public function getParseResults()
    {
        return $this->hasMany(DeviceDetectorResult::class, ['bench_id' => 'id']);
    }

}
