<?php

namespace app\forms;

use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class FinderUserAgentForm extends Model
{
    public $userAgent;
    public $sourceId;
    public $parserId;
    public $modelName;
    public $brandName;
    public $deviceType;
    public $clientType;
    public $clientName;
    public $clientVersion;
    public $osName;
    public $osVersion;
    public $isBot;

    public $notModelName = false;
    public $emptyModelName = false;

    private const PAGE_SIZE = 500;

    public function rules()
    {
        return [
            [[
                'userAgent',
                'modelName',
                'brandName',
                'clientType',
                'clientVersion',
                'clientName',
                'deviceType',
                'osName',
                'osVersion'
            ], 'string'],
            [['sourceId', 'parserId'], 'each', 'rule' => ['integer']],
            [['isBot'], 'integer'],
            [['notModelName', 'emptyModelName'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {

        $labels = parent::attributeLabels();
        $labels['emptyModelName'] = 'All empty Model Name';
        return $labels;
    }

    public function getSourceIdOptions(): array
    {
        return ArrayHelper::map(ParserConfig::REPOSITORIES, 'id', 'name');
    }

    public function getBooleanOptions(): array
    {
        return [0 => 'No', 1 => 'Yes'];
    }

    public function search(array $params = [], ?string $formName = null): DataProviderInterface
    {
        $this->load($params, $formName);
        $query = BenchmarkResult::find();

        $query->andFilterCompare('benchmark_result.user_agent', $this->userAgent, 'like');

        $query->andFilterWhere([
            'benchmark_result.source_id' => $this->sourceId,
            'device_detector_result.parser_id' => $this->parserId,
            'device_detector_result.is_bot' => $this->isBot
        ]);

        $query->andFilterCompare(
            'device_detector_result.model_name',
            $this->modelName,
            !$this->notModelName ? 'like' : 'not like'
        );

        if ($this->emptyModelName) {
            $query->andFilterWhere(['IN', 'device_detector_result.model_name', ['', new Expression('NULL')]]);
        }


        $query->andFilterCompare('device_detector_result.brand_name', $this->brandName, 'like');
        $query->andFilterCompare('device_detector_result.device_type', $this->deviceType, 'like');
        $query->andFilterCompare('device_detector_result.client_type', $this->clientType, 'like');
        $query->andFilterCompare('device_detector_result.client_name', $this->clientName, 'like');
        $query->andFilterCompare('device_detector_result.client_version', $this->clientVersion, 'like');
        $query->andFilterCompare('device_detector_result.os_name', $this->osName, 'like');
        $query->andFilterCompare('device_detector_result.os_version', $this->osVersion, 'like');

        $query->innerJoinWith(['parseResults']);
        $query->groupBy('benchmark_result.id');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::PAGE_SIZE,
            ],
        ]);
    }
}