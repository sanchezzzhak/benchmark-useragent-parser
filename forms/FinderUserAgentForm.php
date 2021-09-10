<?php

namespace app\forms;

use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\helpers\ArrayHelper;

class FinderUserAgentForm extends Model
{
    public $userAgent;
    public $sourceId;

    private const PAGE_SIZE = 1000;

    public function rules()
    {
        return [
            [['userAgent'], 'string'],
            [['sourceId'], 'number'],
        ];
    }

    public function getSourceIdOptions(): array {
        return ArrayHelper::map(ParserConfig::REPOSITORIES, 'id', 'name');
    }

    public function search(array $params = [], ?string $formName = null): DataProviderInterface
    {
        $this->load($params, $formName);
        $query = BenchmarkResult::find();
        $query->andFilterCompare('user_agent', $this->userAgent, 'like');
        $query->andFilterCompare('source_id', $this->sourceId);

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