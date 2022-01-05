<?php

namespace app\grids;

use app\helpers\ParserConfig;
use app\helpers\ParserHelper;
use app\models\BenchmarkResult;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Json;

class FinderUserAgentGrid extends AbstractGrid implements GridInterface
{

    public function getConfig(): array
    {
        return[
            'dataProvider' => $this->getProvider(),
            'tableOptions' => [
                'class' => 'table table-striped'
            ],
            'columns' => $this->columns(),
            'afterRow' => [$this, 'afterResult'],
            'rowOptions' => ['style' => 'background: darkseagreen;']
        ];
    }



    private function userAgentColumn(): array
    {
        return [
            'class' => DataColumn::class,
            'label' => 'User Agent',
            'attribute' => 'user_agent',
            'format' => 'raw',
            'headerOptions' => ['colspan' => 14],
            'contentOptions' => ['colspan' => 14],
            'value' => function (BenchmarkResult $model) {
                return $this->renderHtmlTextArea($model->user_agent, ['onclick' => 'return true']);
            },

        ];
    }

    private function repositoryColumn(): array
    {
        return [
            'class' => DataColumn::class,
            'label' => 'Repository Id',
            'attribute' => 'source_id',
            'contentOptions' => ['style' => 'width:50px;'],
            'format' => 'raw',
            'value' => function (BenchmarkResult $model) {
               return $model->source_id;
            }
        ];
    }

    public function columns(): array
    {
        return [
            'id' => [
                'attribute' => 'id',
                'contentOptions' => ['style' => 'width:100px;'],
            ],
            'repository' => $this->repositoryColumn(),
            'useragent' => $this->userAgentColumn(),
        ];
    }

    public function afterResult(BenchmarkResult $model) {

        $content = [];
        $results = $model->parseResults;


        $content[] = Html::tag('tr',
            ""
            . Html::tag('th', 'Providers')
            . Html::tag('th', 'Bot Name')
            . Html::tag('th', 'IsBot')
            . Html::tag('th', 'Time')
            . Html::tag('th', 'Memory')
            . Html::tag('th', 'Device Type')
            . Html::tag('th', 'Brand Name')
            . Html::tag('th', 'Model Name')
            . Html::tag('th', 'OS')
            . Html::tag('th', 'OS Version')
            . Html::tag('th', 'Client Type')
            . Html::tag('th', 'Client Name')
            . Html::tag('th', 'Client Version')
            . Html::tag('th', 'Engine Name')
            . Html::tag('th', 'Engine Version')
            . Html::tag('th', 'Actions'), [
                'style' => 'background: darkseagreen;'
        ]);

        foreach ($results as $result) {

            $actions = [];
            $actions[] = Html::button('Detect', [
                'class' => 'btn btn-danger btn-sm',
                'data-action' => 're-detect',
                'data-user-agent' => $model->user_agent,
                'data-id' => $result->id,
            ]);
            $actions[] = Html::button('Detail', [
                'class' => 'btn btn-dark btn-sm',
                'data-action' => 'detail',
                'data-json' => $result->data_json
            ]);

            $content[] = Html::tag('tr',
                ""
                . Html::tag('td', ParserConfig::getNameById($result->parser_id))
                . Html::tag('td', $result->bot_name)
                . Html::tag('td', $result->is_bot
                    ? '<i class="bi bi-check2-circle" style="font-size: 30px;"></i>'
                    : '', ['class' => 'text-center'])
                . Html::tag('td', $result->time)
                . Html::tag('td', ParserHelper::formatBytes($result->memory))
                . Html::tag('td', $result->device_type)
                . Html::tag('td', $result->brand_name)
                . Html::tag('td', $result->model_name)
                . Html::tag('td', $result->os_name)
                . Html::tag('td', $result->os_version)
                . Html::tag('td', $result->client_type)
                . Html::tag('td', $result->client_name)
                . Html::tag('td', $result->client_version)
                . Html::tag('td', $result->engine_name)
                . Html::tag('td', $result->engine_version)
                . Html::tag('td', implode(PHP_EOL, $actions))
            );
        }

        return implode(PHP_EOL, $content);
    }


}
