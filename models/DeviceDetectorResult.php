<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device_detector_result".
 *
 * @property int $id
 * @property int $bench_id
 * @property int $parser_id
 * @property float|null $time
 * @property int|null $memory
 * @property string|null $client_name
 * @property string|null $client_version
 * @property string|null $client_type
 * @property string|null $engine_name
 * @property string|null $engine_version
 * @property string|null $os_name
 * @property string|null $os_version
 * @property string|null $data_json
 * @property string|null $device_type
 * @property string|null $brand_name
 * @property string|null $model_name
 * @property bool|null $is_bot
 * @property string|null $bot_name
 */
class DeviceDetectorResult extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device_detector_result';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bench_id', 'parser_id'], 'required'],
            [['bench_id', 'parser_id', 'memory'], 'integer'],
            [['time'], 'number'],
            [['data_json'], 'string'],
            [['is_bot'], 'boolean'],
            [['client_name', 'client_version', 'client_type', 'engine_name', 'engine_version', 'os_name', 'os_version', 'device_type', 'brand_name', 'model_name', 'bot_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bench_id' => 'Bench ID',
            'parser_id' => 'Parser ID',
            'time' => 'Time',
            'memory' => 'Memory',
            'client_name' => 'Client Name',
            'client_version' => 'Client Version',
            'client_type' => 'Client Type',
            'engine_name' => 'Engine Name',
            'engine_version' => 'Engine Version',
            'os_name' => 'Os Name',
            'os_version' => 'Os Version',
            'data_json' => 'Data Json',
            'device_type' => 'Device Type',
            'brand_name' => 'Brand Name',
            'model_name' => 'Model Name',
            'is_bot' => 'Is Bot',
            'bot_name' => 'Bot Name',
        ];
    }

    public static function findOrCreate(int $id, int $parserId): self
    {
        $params = ['bench_id' => $id, 'parser_id' => $parserId];

        $model = self::find()
            ->where($params)
            ->limit(1)
            ->one();

        if ($model === null) {
            $model = new self($params);
        }

        return $model;
    }


}
